<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\FavoriteCity;

class WeatherController extends Controller
{
    // ==========================================
    // SECCIÓN 1: MIS LUGARES (Dashboard)
    // ==========================================
    public function index()
    {
        $cities = Auth::user()->favoriteCities;

        foreach ($cities as $city) {
            $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $city->latitude,
                'longitude' => $city->longitude,
                'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
                'timezone' => 'auto'
            ]);

            if ($response->successful()) {
                $weatherData = $response->json()['current'];
                $weatherData['icon'] = $this->getWeatherIcon($weatherData['weather_code']);
                $weatherData['description'] = $this->getWeatherDescription($weatherData['weather_code']);
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
        if ($city->user_id !== Auth::id()) abort(403);
        $request->validate(['city_name' => 'required|string|max:255']);
        return $this->saveCity($request->city_name, $city);
    }

    public function destroy(FavoriteCity $city)
    {
        if ($city->user_id !== Auth::id()) abort(403);
        $city->delete();
        return back()->with('success', 'Ciudad eliminada.');
    }


    // ==========================================
    // SECCIÓN 2: PANTALLA PRINCIPAL (Home API)
    // ==========================================
    public function home(Request $request)
    {
        $user = Auth::user();
        $cities = $user->favoriteCities;

        $selectedCity = null;
        if ($request->has('city_id')) {
            $selectedCity = $cities->find($request->city_id);
        }
        if (!$selectedCity) {
            $selectedCity = $cities->first();
        }

        if (!$selectedCity) {
            $selectedCity = (object)[
                'id' => null,
                'city_name' => 'Tijuana, Baja California',
                'latitude' => 32.5149,
                'longitude' => -117.0382
            ];
        }

        $weatherResponse = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $selectedCity->latitude,
            'longitude' => $selectedCity->longitude,
            'current' => 'temperature_2m,apparent_temperature,weather_code,relative_humidity_2m,wind_speed_10m,visibility,surface_pressure',
            'hourly' => 'temperature_2m,weather_code',
            'daily' => 'temperature_2m_max,temperature_2m_min,uv_index_max,sunrise,sunset,weather_code,precipitation_probability_max',
            'timezone' => 'auto',
            'forecast_days' => 7
        ]);

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
            
            $currentWeather = $wData['current'];
            $currentWeather['icon'] = $this->getWeatherIcon($currentWeather['weather_code']);
            $currentWeather['description'] = $this->getWeatherDescription($currentWeather['weather_code']);
            $currentWeather['max'] = round($wData['daily']['temperature_2m_max'][0]);
            $currentWeather['min'] = round($wData['daily']['temperature_2m_min'][0]);
            $currentWeather['uv'] = round($wData['daily']['uv_index_max'][0]);
            $currentWeather['sunrise'] = date('g:i a', strtotime($wData['daily']['sunrise'][0]));
            $currentWeather['sunset'] = date('g:i a', strtotime($wData['daily']['sunset'][0]));

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


    // ==========================================
    // SECCIÓN 3: PRONÓSTICO Y CONFIGURACIÓN
    // ==========================================
    
    public function forecast(Request $request)
    {
        $user = Auth::user();
        $cities = $user->favoriteCities;

        // Determinar qué ciudad mostrar
        $selectedCity = null;
        if ($request->has('city_id')) {
            $selectedCity = $cities->find($request->city_id);
        }
        if (!$selectedCity) {
            $selectedCity = $cities->first();
        }
        if (!$selectedCity) {
            $selectedCity = (object)[
                'id' => null, 'city_name' => 'Tijuana, Baja California',
                'latitude' => 32.5149, 'longitude' => -117.0382
            ];
        }

        // Petición a la API para datos súper detallados (14 días y por hora)
        $response = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $selectedCity->latitude,
            'longitude' => $selectedCity->longitude,
            'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,wind_direction_10m,weather_code,precipitation',
            'hourly' => 'temperature_2m,precipitation_probability',
            'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_probability_max,precipitation_sum',
            'timezone' => 'auto',
            'forecast_days' => 14
        ]);

        $current = null;
        $hourly = [];
        $maxTempHourly = 1; // Para escalar la gráfica

        if ($response->successful()) {
            $data = $response->json();
            
            // 1. Datos actuales y del día
            $current = $data['current'];
            $current['desc'] = $this->getWeatherDescription($current['weather_code']);
            $current['icon'] = $this->getWeatherIcon($current['weather_code']);
            $current['max'] = round($data['daily']['temperature_2m_max'][0]);
            $current['min'] = round($data['daily']['temperature_2m_min'][0]);
            $current['prob_lluvia'] = $data['daily']['precipitation_probability_max'][0];
            $current['lluvia_total'] = $data['daily']['precipitation_sum'][0];
            
            // Calcular dirección del viento (ej. 315° -> NO)
            $val = floor(($current['wind_direction_10m'] / 22.5) + 0.5);
            $arr = ["N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S", "SSW", "SO", "OSO", "O", "ONO", "NO", "NNO"];
            $current['wind_dir_text'] = $arr[($val % 16)];

            // 2. Datos por hora (próximas 12 horas)
            $currentHour = (int)date('H');
            for ($i = 0; $i <= 11; $i++) {
                $idx = $currentHour + $i;
                if (isset($data['hourly']['time'][$idx])) {
                    $temp = round($data['hourly']['temperature_2m'][$idx]);
                    $hourly[] = [
                        'time' => date('H:i', strtotime($data['hourly']['time'][$idx])),
                        'temp' => $temp,
                        'prob' => $data['hourly']['precipitation_probability'][$idx]
                    ];
                    if ($temp > $maxTempHourly) $maxTempHourly = $temp;
                }
            }
        }

        return view('forecast', compact('selectedCity', 'cities', 'current', 'hourly', 'maxTempHourly'));
    }

    public function settings()
    {
        return view('settings');
    }


    // ==========================================
    // SECCIÓN 4: FUNCIONES PRIVADAS (Helpers)
    // ==========================================

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