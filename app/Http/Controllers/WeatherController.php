<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\FavoriteCity;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    // ==========================================
    // SECCIÓN 1: MIS LUGARES (Dashboard)
    // ==========================================
    public function index()
    {
        $cities = Auth::user()->favoriteCities;
        $units = $this->getUnits();

        foreach ($cities as $city) {
            $cacheKey = "dashboard_v2_{$city->latitude}_{$city->longitude}_temp_" . session('pref_temp', 'celsius') . "_wind_" . session('pref_wind', 'kmh');
            
            $fullData = Cache::remember($cacheKey, 3600, function () use ($city) {
                $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $city->latitude,
                    'longitude' => $city->longitude,
                    'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
                    'daily' => 'temperature_2m_max,temperature_2m_min',
                    'timezone' => 'auto',
                    'temperature_unit' => session('pref_temp', 'celsius'),
                    'wind_speed_unit' => session('pref_wind', 'kmh'),
                ]);
                return $response->successful() ? $response->json() : null;
            });

            if ($fullData) {
                $weatherData = $fullData['current'] ?? [];
                $weatherData['icon'] = $this->getWeatherIcon($weatherData['weather_code'] ?? 0);
                $weatherData['description'] = $this->getWeatherDescription($weatherData['weather_code'] ?? 0);
                
                if (isset($fullData['daily'])) {
                    $weatherData['max'] = round($fullData['daily']['temperature_2m_max'][0]);
                    $weatherData['min'] = round($fullData['daily']['temperature_2m_min'][0]);
                }

                $city->weather = $weatherData;
            }
        }

        return view('dashboard', compact('cities', 'units'));
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
        $units = $this->getUnits();

        $selectedCity = $request->has('city_id') ? $cities->find($request->city_id) : $cities->first();
        
        if (!$selectedCity) {
            $selectedCity = (object)[
                'id' => null, 'city_name' => 'Tijuana, Baja California',
                'latitude' => 32.5149, 'longitude' => -117.0382
            ];
        }

        // OBLIGAMOS A DEBUGBAR A MEDIR ESTE BLOQUE EXACTO
        \Debugbar::startMeasure('api_clima', 'Tiempo de respuesta: API Clima');
        
        $cacheKeyWeather = "home_v2_{$selectedCity->latitude}_{$selectedCity->longitude}_temp_" . session('pref_temp', 'celsius') . "_wind_" . session('pref_wind', 'kmh');
        $wData = Cache::remember($cacheKeyWeather, 3600, function () use ($selectedCity) {
            $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $selectedCity->latitude,
                'longitude' => $selectedCity->longitude,
                'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,wind_speed_10m,wind_direction_10m,weather_code,precipitation,visibility,surface_pressure',
                'hourly' => 'temperature_2m,weather_code',
                'daily' => 'temperature_2m_max,temperature_2m_min,uv_index_max,sunrise,sunset,weather_code,precipitation_probability_max',
                'timezone' => 'auto',
                'forecast_days' => 7,
                'temperature_unit' => session('pref_temp', 'celsius'),
                'wind_speed_unit' => session('pref_wind', 'kmh'),
            ]);
            return $response->successful() ? $response->json() : null;
        });

        \Debugbar::stopMeasure('api_clima');

        // MEDIMOS TAMBIÉN LA CALIDAD DEL AIRE
        \Debugbar::startMeasure('api_aqi', 'Tiempo de respuesta: API Calidad del Aire');

        $airQuality = Cache::remember("aqi_home_{$selectedCity->latitude}_{$selectedCity->longitude}", 3600, function () use ($selectedCity) {
            $response = Http::get('https://air-quality-api.open-meteo.com/v1/air-quality', [
                'latitude' => $selectedCity->latitude,
                'longitude' => $selectedCity->longitude,
                'current' => 'us_aqi,pm10,pm2_5,carbon_monoxide'
            ]);
            return $response->successful() ? $response->json()['current'] : null;
        });

        \Debugbar::stopMeasure('api_aqi');

        $currentWeather = null; $hourlyWeather = []; $dailyWeather = [];

        if ($wData) {
            $currentWeather = $wData['current'];
            
            // CORRECCIONES Y VALORES DE RESCATE (Para evitar colapsos)
            $currentWeather['apparent_temperature'] = $currentWeather['apparent_temperature'] ?? $currentWeather['temperature_2m'];
            
            $currentWeather['icon'] = $this->getWeatherIcon($currentWeather['weather_code']);
            $currentWeather['description'] = $this->getWeatherDescription($currentWeather['weather_code']);
            $currentWeather['max'] = round($wData['daily']['temperature_2m_max'][0]);
            $currentWeather['min'] = round($wData['daily']['temperature_2m_min'][0]);
            $currentWeather['uv'] = round($wData['daily']['uv_index_max'][0]);
            $currentWeather['sunrise'] = date('g:i a', strtotime($wData['daily']['sunrise'][0]));
            $currentWeather['sunset'] = date('g:i a', strtotime($wData['daily']['sunset'][0]));
            
            $vis = $currentWeather['visibility'] ?? 10000;
            $currentWeather['vis_val'] = session('pref_dist', 'km') === 'mi' ? round($vis / 1609.34) : round($vis / 1000);
            
            $press = $currentWeather['surface_pressure'] ?? 1013;
            $currentWeather['press_val'] = session('pref_press', 'hpa') === 'mmhg' ? round($press * 0.750062) : round($press);

            $currentHour = (int)date('H');
            for ($i = 0; $i < 6; $i++) {
                if (isset($wData['hourly']['time'][$currentHour + $i])) {
                    $hourlyWeather[] = [
                        'hora' => $i == 0 ? 'Ahora' : date('H:i', strtotime($wData['hourly']['time'][$currentHour + $i])),
                        'temp' => round($wData['hourly']['temperature_2m'][$currentHour + $i]) . '°',
                        'icon' => $this->getWeatherIcon($wData['hourly']['weather_code'][$currentHour + $i])
                    ];
                }
            }

            $daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
            for ($i = 0; $i < 4; $i++) {
                $dailyWeather[] = [
                    'dia' => ($i == 0) ? 'Hoy' : (($i == 1) ? 'Mañana' : $daysOfWeek[date('w', strtotime($wData['daily']['time'][$i]))]),
                    'icon' => $this->getWeatherIcon($wData['daily']['weather_code'][$i]),
                    'desc' => $this->getWeatherDescription($wData['daily']['weather_code'][$i]),
                    'prob_lluvia' => $wData['daily']['precipitation_probability_max'][$i],
                    'max' => round($wData['daily']['temperature_2m_max'][$i]),
                    'min' => round($wData['daily']['temperature_2m_min'][$i]),
                ];
            }
        }

        return view('home', compact('selectedCity', 'cities', 'currentWeather', 'hourlyWeather', 'dailyWeather', 'airQuality', 'units'));
    }

    // ==========================================
    // SECCIÓN 3: PRONÓSTICO Y CONFIGURACIÓN
    // ==========================================
    public function forecast(Request $request)
    {
        $user = Auth::user();
        $cities = $user->favoriteCities;
        $units = $this->getUnits();
        $selectedCity = $request->has('city_id') ? $cities->find($request->city_id) : $cities->first();

        if (!$selectedCity) {
            $selectedCity = (object)['id' => null, 'city_name' => 'Tijuana, Baja California', 'latitude' => 32.5149, 'longitude' => -117.0382];
        }

        // Obligamos a ignorar caché corrupta usando "_v3"
        $cacheKeyForecast = "forecast_v3_{$selectedCity->latitude}_{$selectedCity->longitude}_temp_" . session('pref_temp', 'celsius') . "_wind_" . session('pref_wind', 'kmh');
        
        $data = Cache::remember($cacheKeyForecast, 3600, function () use ($selectedCity) {
            $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $selectedCity->latitude,
                'longitude' => $selectedCity->longitude,
                'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,wind_direction_10m,weather_code,precipitation,visibility,surface_pressure',
                'hourly' => 'temperature_2m,precipitation_probability',
                'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_probability_max,precipitation_sum,weather_code',
                'timezone' => 'auto',
                'forecast_days' => 14,
                'temperature_unit' => session('pref_temp', 'celsius'),
                'wind_speed_unit' => session('pref_wind', 'kmh'),
            ]);
            return $response->successful() ? $response->json() : null;
        });

        $current = null; $hourly = []; $daily = [];

        if ($data) {
            $current = $data['current'];
            $current['desc'] = $this->getWeatherDescription($current['weather_code']);
            $current['icon'] = $this->getWeatherIcon($current['weather_code']);
            $current['max'] = round($data['daily']['temperature_2m_max'][0]);
            $current['min'] = round($data['daily']['temperature_2m_min'][0]);
            
            $current['prob_lluvia'] = $data['daily']['precipitation_probability_max'][0];
            $current['lluvia_total'] = $data['daily']['precipitation_sum'][0];

            // VALORES DE RESCATE (Previene fallos de renderizado)
            if (isset($current['visibility'])) {
                $current['vis_val'] = session('pref_dist', 'km') === 'mi' ? round($current['visibility'] / 1609.34) : round($current['visibility'] / 1000);
            } else {
                $current['vis_val'] = 0;
            }

            if (isset($current['surface_pressure'])) {
                $current['press_val'] = session('pref_press', 'hpa') === 'mmhg' ? round($current['surface_pressure'] * 0.750062) : round($current['surface_pressure']);
            } else {
                $current['press_val'] = 0;
            }

            $windDir = $current['wind_direction_10m'] ?? 0;
            $arr = ["N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S", "SSW", "SO", "OSO", "O", "ONO", "NO", "NNO"];
            $current['wind_dir_text'] = $arr[floor(($windDir / 22.5) + 0.5) % 16];

            for ($i = 0; $i <= 11; $i++) {
                if (isset($data['hourly']['time'][(int)date('H') + $i])) {
                    $hourly[] = [
                        'time' => date('H:i', strtotime($data['hourly']['time'][(int)date('H') + $i])),
                        'temp' => round($data['hourly']['temperature_2m'][(int)date('H') + $i]),
                        'prob' => $data['hourly']['precipitation_probability'][(int)date('H') + $i]
                    ];
                }
            }

            for ($i = 0; $i < 14; $i++) {
                if (isset($data['daily']['time'][$i])) {
                    $daily[] = [
                        'date' => ($i == 0) ? 'Hoy' : (($i == 1) ? 'Mañana' : ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'][date('w', strtotime($data['daily']['time'][$i]))] . ' ' . date('d', strtotime($data['daily']['time'][$i]))),
                        'icon' => $this->getWeatherIcon($data['daily']['weather_code'][$i]),
                        'desc' => $this->getWeatherDescription($data['daily']['weather_code'][$i]),
                        'max' => round($data['daily']['temperature_2m_max'][$i]),
                        'min' => round($data['daily']['temperature_2m_min'][$i]),
                        'prob' => $data['daily']['precipitation_probability_max'][$i],
                    ];
                }
            }
        }

        return view('forecast', compact('selectedCity', 'cities', 'current', 'hourly', 'daily', 'units'));
    }

    public function settings() { return view('settings'); }

    public function updateSettings(Request $request)
    {
        session([
            'pref_temp' => $request->pref_temp ?? 'celsius',
            'pref_wind' => $request->pref_wind ?? 'kmh',
            'pref_dist' => $request->pref_dist ?? 'km',
            'pref_press' => $request->pref_press ?? 'hpa',
        ]);
        return back()->with('success', 'Preferencias guardadas correctamente.');
    }

    private function getUnits()
    {
        return [
            'temp' => session('pref_temp', 'celsius') === 'fahrenheit' ? '°F' : '°C',
            'wind' => session('pref_wind', 'kmh') === 'mph' ? 'mph' : (session('pref_wind', 'kmh') === 'ms' ? 'm/s' : 'km/h'),
            'dist' => session('pref_dist', 'km') === 'mi' ? 'mi' : 'km',
            'press' => session('pref_press', 'hpa') === 'mmhg' ? 'mmHg' : 'hPa',
        ];
    }

    private function saveCity($name, $cityModel = null)
    {
        $response = Http::get('https://geocoding-api.open-meteo.com/v1/search', ['name' => $name, 'count' => 1, 'language' => 'es', 'format' => 'json']);
        if ($response->successful() && isset($response->json()['results'][0])) {
            $loc = $response->json()['results'][0];
            $data = ['city_name' => $loc['name'] . (isset($loc['admin1']) ? ', ' . $loc['admin1'] : ''), 'latitude' => $loc['latitude'], 'longitude' => $loc['longitude']];
            $cityModel ? $cityModel->update($data) : Auth::user()->favoriteCities()->create($data);
            return back()->with('success', 'Ciudad procesada correctamente.');
        }
        return back()->withErrors(['city_name' => 'No se encontró la ubicación exacta.']);
    }

    private function getWeatherIcon($code)
    {
        $icons = [0=>'☀️', 1=>'🌤️', 2=>'⛅', 3=>'☁️', 45=>'🌫️', 48=>'🌫️', 51=>'🌧️', 53=>'🌧️', 55=>'🌧️', 61=>'🌧️', 63=>'🌧️', 65=>'🌧️', 71=>'❄️', 73=>'❄️', 75=>'❄️', 95=>'⛈️', 96=>'⛈️', 99=>'⛈️'];
        return $icons[$code] ?? '☁️';
    }

    private function getWeatherDescription($code)
    {
        $descriptions = [0=>'Despejado', 1=>'Mayormente despejado', 2=>'Parcialmente nublado', 3=>'Nublado', 45=>'Niebla', 48=>'Niebla escarchada', 51=>'Llovizna ligera', 53=>'Llovizna moderada', 55=>'Llovizna densa', 61=>'Lluvia leve', 63=>'Lluvia moderada', 65=>'Lluvia fuerte', 71=>'Nieve leve', 73=>'Nieve moderada', 75=>'Nieve fuerte', 95=>'Tormenta', 96=>'Tormenta con granizo', 99=>'Tormenta fuerte con granizo'];
        return $descriptions[$code] ?? 'Desconocido';
    }
}