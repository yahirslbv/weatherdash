<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\FavoriteCity;

class WeatherController extends Controller
{
    public function index()
    {
        $cities = Auth::user()->favoriteCities;

        // Recorremos las ciudades guardadas para buscar su clima en tiempo real
        foreach ($cities as $city) {
            $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $city->latitude,
                'longitude' => $city->longitude,
                'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
                'timezone' => 'auto'
            ]);

            // Si la API responde correctamente
            if ($response->successful()) {
                // 1. Guardamos la respuesta en una variable temporal
                $weatherData = $response->json()['current'];
                
                // 2. Le agregamos nuestros campos traducidos a la variable temporal
                $weatherData['icon'] = $this->getWeatherIcon($weatherData['weather_code']);
                $weatherData['description'] = $this->getWeatherDescription($weatherData['weather_code']);
                
                // 3. Ahora sí, le pasamos el arreglo completo y armado a nuestro modelo
                $city->weather = $weatherData;
            }
        }

        return view('dashboard', compact('cities'));
    }

    public function store(Request $request)
    {
        $request->validate(['city_name' => 'required|string|max:255']);

        return $this->saveCity($request->city_name);
    }

    public function update(Request $request, FavoriteCity $city)
    {
        // Validar que el usuario sea el dueño de este registro
        if ($city->user_id !== Auth::id()) abort(403);

        $request->validate(['city_name' => 'required|string|max:255']);

        // Al editar, volvemos a buscar las coordenadas
        return $this->saveCity($request->city_name, $city);
    }

    public function destroy(FavoriteCity $city)
    {
        if ($city->user_id !== Auth::id()) abort(403);
        
        $city->delete();
        return back()->with('success', 'Ciudad eliminada.');
    }

    public function home(Request $request)
    {
        $user = Auth::user();
        $cities = $user->favoriteCities;

        // 1. Elegir qué ciudad mostrar (si hay un parámetro en la URL, o la primera guardada, o Tijuana por defecto)
        $selectedCity = null;
        if ($request->has('city_id')) {
            $selectedCity = $cities->find($request->city_id);
        }
        if (!$selectedCity) {
            $selectedCity = $cities->first();
        }

        // Fallback idéntico al prototipo si la base de datos está vacía
        if (!$selectedCity) {
            $selectedCity = (object)[
                'id' => null,
                'city_name' => 'Tijuana, Baja California',
                'latitude' => 32.5149,
                'longitude' => -117.0382
            ];
        }

        // 2. Consultar Clima actual, Pronóstico por hora y Pronóstico de 7 días
        $weatherResponse = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $selectedCity->latitude,
            'longitude' => $selectedCity->longitude,
            'current' => 'temperature_2m,apparent_temperature,weather_code,relative_humidity_2m,wind_speed_10m,visibility,surface_pressure',
            'hourly' => 'temperature_2m,weather_code',
            'daily' => 'temperature_2m_max,temperature_2m_min,uv_index_max,sunrise,sunset,weather_code,precipitation_probability_max',
            'timezone' => 'auto',
            'forecast_days' => 7
        ]);

        // 3. Consultar Calidad del Aire (Métricas inferiores de tu Figma)
        $aqiResponse = Http::get('https://air-quality-api.open-meteo.com/v1/air-quality', [
            'latitude' => $selectedCity->latitude,
            'longitude' => $selectedCity->longitude,
            'current' => 'us_aqi,pm10,pm2_5,carbon_monoxide'
        ]);

        $currentWeather = null;
        $hourlyWeather = [];
        $dailyWeather = [];
        $airQuality = null;

        if ($weatherResponse->successful()) {
            $wData = $weatherResponse->json();
            
            // Estructurar Clima Actual e inyectar detalles del día (índice 0)
            $currentWeather = $wData['current'];
            $currentWeather['icon'] = $this->getWeatherIcon($currentWeather['weather_code']);
            $currentWeather['description'] = $this->getWeatherDescription($currentWeather['weather_code']);
            $currentWeather['max'] = round($wData['daily']['temperature_2m_max'][0]);
            $currentWeather['min'] = round($wData['daily']['temperature_2m_min'][0]);
            $currentWeather['uv'] = round($wData['daily']['uv_index_max'][0]);
            $currentWeather['sunrise'] = date('g:i a', strtotime($wData['daily']['sunrise'][0]));
            $currentWeather['sunset'] = date('g:i a', strtotime($wData['daily']['sunset'][0]));

            // Estructurar Pronóstico por hora (siguientes 6 intervalos desde la hora actual)
            $currentHour = (int)date('H');
            for ($i = 0; $i < 6; $i++) {
                $index = $currentHour + $i;
                if (isset($wData['hourly']['time'][$index])) {
                    $hourlyWeather[] = [
                        'hora' => $i == 0 ? 'Ahora' : date('H:i', strtotime($wData['hourly']['time'][$index])),
                        'temp' => round($wData['hourly']['temperature_2m'][$index]) . '°',
                        'icon' => $this->getWeatherIcon($wData['hourly']['weather_code'][$index])
                    ];
                }
            }

            // Estructurar Pronóstico a 7 días (primeros 4 días destacados del diseño)
            $daysOfWeek = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            for ($i = 0; $i < 4; $i++) {
                $timeString = strtotime($wData['daily']['time'][$i]);
                $dayLabel = ($i == 0) ? 'Hoy' : (($i == 1) ? 'Mañana' : $daysOfWeek[date('w', $timeString)]);

                $dailyWeather[] = [
                    'dia' => $dayLabel,
                    'icon' => $this->getWeatherIcon($wData['daily']['weather_code'][$i]),
                    'desc' => $this->getWeatherDescription($wData['daily']['weather_code'][$i]),
                    'prob_lluvia' => $wData['daily']['precipitation_probability_max'][$i],
                    'max' => round($wData['daily']['temperature_2m_max'][$i]),
                    'min' => round($wData['daily']['temperature_2m_min'][$i]),
                ];
            }
        }

        if ($aqiResponse->successful()) {
            $airQuality = $aqiResponse->json()['current'];
        }

        return view('home', compact('selectedCity', 'cities', 'currentWeather', 'hourlyWeather', 'dailyWeather', 'airQuality'));
    }
    // Método privado para evitar repetir la consulta a la API de Open-Meteo
    private function saveCity($name, $cityModel = null)
    {
        $response = Http::get('https://geocoding-api.open-meteo.com/v1/search', [
            'name' => $name,
            'count' => 1,
            'language' => 'es',
            'format' => 'json'
        ]);

        if ($response->successful() && isset($response->json()['results'][0])) {
            $location = $response->json()['results'][0];
            $data = [
                'city_name' => $location['name'] . (isset($location['admin1']) ? ', ' . $location['admin1'] : ''),
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
            ];

            if ($cityModel) {
                $cityModel->update($data);
                $msg = 'Ciudad actualizada correctamente.';
            } else {
                Auth::user()->favoriteCities()->create($data);
                $msg = 'Ciudad agregada correctamente.';
            }

            return back()->with('success', $msg);
        }

        return back()->withErrors(['city_name' => 'No se encontró la ubicación exacta.']);
    }

    // Traduce los códigos de Open-Meteo a emojis
    private function getWeatherIcon($code)
    {
        $icons = [
            0 => '☀️', 1 => '🌤️', 2 => '⛅', 3 => '☁️',
            45 => '🌫️', 48 => '🌫️', 51 => '🌧️', 53 => '🌧️', 55 => '🌧️',
            61 => '🌧️', 63 => '🌧️', 65 => '🌧️', 71 => '❄️', 73 => '❄️',
            75 => '❄️', 95 => '⛈️', 96 => '⛈️', 99 => '⛈️'
        ];
        return $icons[$code] ?? '☁️';
    }

    // Traduce los códigos de Open-Meteo a descripciones en español
    private function getWeatherDescription($code)
    {
        $descriptions = [
            0 => 'Despejado', 1 => 'Mayormente despejado', 2 => 'Parcialmente nublado', 3 => 'Nublado',
            45 => 'Niebla', 48 => 'Niebla escarchada', 51 => 'Llovizna ligera', 53 => 'Llovizna moderada',
            55 => 'Llovizna densa', 61 => 'Lluvia leve', 63 => 'Lluvia moderada', 65 => 'Lluvia fuerte',
            71 => 'Nieve leve', 73 => 'Nieve moderada', 75 => 'Nieve fuerte', 95 => 'Tormenta',
            96 => 'Tormenta con granizo', 99 => 'Tormenta fuerte con granizo'
        ];
        return $descriptions[$code] ?? 'Desconocido';
    }
}