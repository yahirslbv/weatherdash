<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FavoriteCity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WeatherControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Limpiamos la caché antes de cada prueba para evitar interferencias
        Cache::flush(); 
        // Creamos un usuario base para nuestras pruebas
        $this->user = User::factory()->create();
    }

    /** 1. Prueba de redirección raíz (Feature) */
    public function test_root_url_redirects_to_home()
    {
        $response = $this->get('/');
        $response->assertRedirect(route('home'));
    }

    /** 2. Prueba de seguridad de rutas (Feature) */
    public function test_unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    /** 3. Prueba de acceso seguro al dashboard (Feature) */
    public function test_authenticated_user_can_access_dashboard()
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    /** 4. Prueba de acceso a configuraciones (Feature) */
    public function test_authenticated_user_can_access_settings()
    {
        $response = $this->actingAs($this->user)->get(route('settings'));
        $response->assertStatus(200);
    }

    /** 5. Prueba de actualización de configuraciones en sesión (Feature) */
    public function test_user_can_update_settings_preferences()
    {
        $response = $this->actingAs($this->user)->post(route('settings.update'), [
            'pref_temp' => 'fahrenheit',
            'pref_wind' => 'mph',
            'pref_dist' => 'mi',
            'pref_press' => 'mmhg',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertEquals('fahrenheit', session('pref_temp'));
        $this->assertEquals('mph', session('pref_wind'));
    }

    /** 6. Prueba de validación de ciudad vacía (Unit/Feature) */
    public function test_store_city_validation_fails_if_name_is_empty()
    {
        $response = $this->actingAs($this->user)->post(route('city.store'), [
            'city_name' => '',
        ]);

        $response->assertSessionHasErrors('city_name');
    }

    /** 7. Prueba con Http::fake para geocodificación exitosa (Feature) */
    public function test_user_can_store_favorite_city_with_http_fake()
    {
        // Simulamos la respuesta de la API de Geocoding
        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response([
                'results' => [
                    ['name' => 'Monterrey', 'admin1' => 'Nuevo León', 'latitude' => 25.68, 'longitude' => -100.31]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($this->user)->post(route('city.store'), [
            'city_name' => 'Monterrey'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('favorite_cities', [
            'user_id' => $this->user->id,
            'city_name' => 'Monterrey, Nuevo León'
        ]);
    }

    /** 8. Prueba con Http::fake para ciudad no encontrada (Feature) */
    public function test_user_cannot_store_city_if_geocoding_returns_no_results()
    {
        // Simulamos que la API no encontró la ciudad
        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response(['results' => []], 200)
        ]);

        $response = $this->actingAs($this->user)->post(route('city.store'), [
            'city_name' => 'CiudadFalsa123'
        ]);

        $response->assertSessionHasErrors('city_name');
        $this->assertDatabaseMissing('favorite_cities', [
            'city_name' => 'CiudadFalsa123'
        ]);
    }

    /** 9. Prueba con Http::fake para actualizar ciudad (Feature) */
    public function test_user_can_update_their_own_favorite_city()
    {
        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response([
                'results' => [['name' => 'Guadalajara', 'admin1' => 'Jalisco', 'latitude' => 20.65, 'longitude' => -103.34]]
            ], 200)
        ]);

        $city = FavoriteCity::create(['user_id' => $this->user->id, 'city_name' => 'ViejaCiudad', 'latitude' => 0, 'longitude' => 0]);

        $response = $this->actingAs($this->user)->patch(route('city.update', $city), [
            'city_name' => 'Guadalajara'
        ]);

        $this->assertDatabaseHas('favorite_cities', [
            'id' => $city->id,
            'city_name' => 'Guadalajara, Jalisco'
        ]);
    }

    /** 10. Prueba de autorización al actualizar (Seguridad) */
    public function test_user_cannot_update_another_users_favorite_city()
    {
        $otherUser = User::factory()->create();
        $otherCity = FavoriteCity::create(['user_id' => $otherUser->id, 'city_name' => 'Ciudad Ajena', 'latitude' => 0, 'longitude' => 0]);

        $response = $this->actingAs($this->user)->patch(route('city.update', $otherCity), [
            'city_name' => 'Mi Ciudad'
        ]);

        $response->assertStatus(403); // Forbidden
    }

    /** 11. Prueba de borrado de ciudad propia (Feature) */
    public function test_user_can_delete_their_own_favorite_city()
    {
        $city = FavoriteCity::create(['user_id' => $this->user->id, 'city_name' => 'Tijuana', 'latitude' => 32.5, 'longitude' => -117]);

        $response = $this->actingAs($this->user)->delete(route('city.destroy', $city));

        $response->assertRedirect();
        $this->assertDatabaseMissing('favorite_cities', ['id' => $city->id]);
    }

    /** 12. Prueba de autorización al borrar (Seguridad) */
    public function test_user_cannot_delete_another_users_favorite_city()
    {
        $otherUser = User::factory()->create();
        $otherCity = FavoriteCity::create(['user_id' => $otherUser->id, 'city_name' => 'Ciudad Ajena', 'latitude' => 0, 'longitude' => 0]);

        $response = $this->actingAs($this->user)->delete(route('city.destroy', $otherCity));

        $response->assertStatus(403);
    }

    /** 13. Prueba con Http::fake para carga del Home (Feature/API) */
    public function test_home_loads_weather_data_with_http_fake()
    {
        $this->fakeWeatherAPI();

        $response = $this->actingAs($this->user)->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewHas('currentWeather');
        $response->assertViewHas('airQuality');
    }

    /** 14. Prueba con Http::fake para vista Forecast (Feature/API) */
    public function test_forecast_loads_weather_data_with_http_fake()
    {
        $this->fakeWeatherAPI();

        $response = $this->actingAs($this->user)->get(route('forecast'));

        $response->assertStatus(200);
        $response->assertViewHas('current');
        $response->assertViewHas('daily');
    }

    /** 15. Prueba con Http::fake para iteración del Dashboard (Feature/API) */
    public function test_dashboard_loads_weather_data_for_cities_with_http_fake()
    {
        $this->fakeWeatherAPI();

        FavoriteCity::create(['user_id' => $this->user->id, 'city_name' => 'Tijuana', 'latitude' => 32.5, 'longitude' => -117]);
        FavoriteCity::create(['user_id' => $this->user->id, 'city_name' => 'Mexicali', 'latitude' => 32.6, 'longitude' => -115.4]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('cities');
    }

    /** 16. Prueba de Ciudad por Defecto en Home (Feature) */
    public function test_home_uses_default_city_if_user_has_no_favorites()
    {
        $this->fakeWeatherAPI();

        $response = $this->actingAs($this->user)->get(route('home'));

        $viewData = $response->original->getData();
        $this->assertEquals('Tijuana, Baja California', $viewData['selectedCity']->city_name);
    }

    /** 17. Prueba de selección de ciudad específica (Feature) */
    public function test_home_loads_specific_city_if_requested()
    {
        $this->fakeWeatherAPI();
        
        $city = FavoriteCity::create(['user_id' => $this->user->id, 'city_name' => 'Ensenada', 'latitude' => 31.86, 'longitude' => -116.59]);

        $response = $this->actingAs($this->user)->get(route('home', ['city_id' => $city->id]));

        $viewData = $response->original->getData();
        $this->assertEquals('Ensenada', $viewData['selectedCity']->city_name);
    }

    /** 18. Prueba de lógica interna de caché y unidades (Unit/Feature) */
    public function test_units_are_correctly_applied_from_session()
    {
        $this->fakeWeatherAPI();
        
        session(['pref_temp' => 'fahrenheit']);
        
        $response = $this->actingAs($this->user)->get(route('home'));
        $viewData = $response->original->getData();
        
        // Verifica que la vista reciba el arreglo de unidades modificado por la sesión
        $this->assertEquals('°F', $viewData['units']['temp']);
    }

    /**
     * Helper interno para simular respuestas completas de las APIs del clima.
     * Esto evita que falle por llaves no definidas y cumple el requisito Http::fake
     */
    private function fakeWeatherAPI()
    {
        Http::fake([
            'api.open-meteo.com/*' => Http::response([
                'current' => [
                    'temperature_2m' => 22, 'apparent_temperature' => 23, 'weather_code' => 1,
                    'relative_humidity_2m' => 60, 'wind_speed_10m' => 15, 'wind_direction_10m' => 180,
                    'visibility' => 10000, 'surface_pressure' => 1012, 'precipitation' => 0
                ],
                'hourly' => [
                    'time' => array_fill(0, 48, date('Y-m-d\TH:00')),
                    'temperature_2m' => array_fill(0, 48, 22),
                    'weather_code' => array_fill(0, 48, 1),
                    'precipitation_probability' => array_fill(0, 48, 10)
                ],
                'daily' => [
                    'time' => array_fill(0, 14, date('Y-m-d')),
                    'temperature_2m_max' => array_fill(0, 14, 28),
                    'temperature_2m_min' => array_fill(0, 14, 16),
                    'uv_index_max' => array_fill(0, 14, 6),
                    'sunrise' => array_fill(0, 14, date('Y-m-d\T06:00')),
                    'sunset' => array_fill(0, 14, date('Y-m-d\T19:00')),
                    'weather_code' => array_fill(0, 14, 1),
                    'precipitation_probability_max' => array_fill(0, 14, 10),
                    'precipitation_sum' => array_fill(0, 14, 0)
                ]
            ], 200),
            'air-quality-api.open-meteo.com/*' => Http::response([
                'current' => ['us_aqi' => 35, 'pm10' => 10, 'pm2_5' => 5, 'carbon_monoxide' => 150]
            ], 200)
        ]);
    }
}