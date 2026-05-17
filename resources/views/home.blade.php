<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Dashboard</h2>
                    <p class="text-slate-500 mt-1">Resumen meteorológico en tiempo real</p>
                </div>
                
                @if($cities->count() > 0)
                    <div>
                        <form action="{{ route('home') }}" method="GET">
                            <select name="city_id" onchange="this.form.submit()" class="border-gray-200 bg-white rounded-xl shadow-sm text-sm font-medium text-slate-700 focus:ring-blue-500 focus:border-blue-500 py-2.5 pl-4 pr-10">
                                @foreach($cities as $c)
                                    <option value="{{ $c->id }}" {{ $selectedCity->id == $c->id ? 'selected' : '' }}>
                                        📍 {{ explode(',', $c->city_name)[0] }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif
            </div>

            @if($currentWeather)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <div class="lg:col-span-2 space-y-6">
                        
                        <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl shadow-md p-8 text-white relative overflow-hidden">
                            <div class="absolute -right-6 -top-6 text-9xl opacity-15 select-none">{{ $currentWeather['icon'] }}</div>
                            
                            <div class="relative z-10 flex justify-between items-start">
                                <div>
                                    <h3 class="text-3xl font-bold tracking-tight">{{ explode(',', $selectedCity->city_name)[0] }}</h3>
                                    <p class="text-blue-100 text-sm mt-0.5">{{ count(explode(',', $selectedCity->city_name)) > 1 ? trim(explode(',', $selectedCity->city_name)[1]) : '' }}</p>
                                    <p class="text-blue-500 bg-blue-50 font-semibold px-3 py-1 rounded-full text-xs mt-4 inline-block shadow-sm">
                                        {{ $currentWeather['description'] }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="text-6xl font-extralight tracking-tighter">{{ round($currentWeather['temperature_2m']) }}°C</div>
                                </div>
                            </div>
                            
                            <div class="relative z-10 mt-10 flex items-end justify-between border-t border-blue-400/40 pt-5">
                                <div class="text-sm space-y-0.5">
                                    <p class="text-blue-100">Sensación térmica: <span class="font-semibold text-white">{{ round($currentWeather['apparent_temperature']) }}°C</span></p>
                                    <p class="text-blue-100">Rango del día: <span class="font-semibold text-white">↑{{ $currentWeather['max'] }}° / ↓{{ $currentWeather['min'] }}°</span></p>
                                </div>
                                <div class="text-xs bg-black/15 px-3 py-1.5 rounded-lg backdrop-blur-sm text-blue-100">
                                    Llamada en vivo realizada con éxito
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-base font-bold text-slate-800 tracking-tight">Pronóstico por hora</h3>
                                <a href="{{ route('forecast') }}" class="text-blue-600 text-xs font-semibold hover:underline">Ver todo →</a>
                            </div>
                            
                            <div class="flex justify-between items-center overflow-x-auto pb-2 gap-4">
                                @foreach($hourlyWeather as $hour)
                                    <div class="flex flex-col items-center min-w-[65px] bg-slate-50/60 py-3 rounded-xl border border-slate-100">
                                        <span class="text-xs text-slate-400 font-medium mb-1.5">{{ $hour['hora'] }}</span>
                                        <span class="text-2xl mb-1.5 select-none">{{ $hour['icon'] }}</span>
                                        <span class="font-bold text-slate-800 text-sm">{{ $hour['temp'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-2 text-slate-400 text-sm mb-3 font-medium">
                                        <span>🍃</span> <h4>Calidad del Aire</h4>
                                    </div>
                                    <div class="text-3xl font-bold text-slate-800 tracking-tight">
                                        AQI {{ $airQuality['us_aqi'] ?? '--' }}
                                        <span class="text-sm font-bold ml-2 px-2 py-0.5 rounded {{ ($airQuality['us_aqi'] ?? 0) <= 50 ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600' }}">
                                            {{ ($airQuality['us_aqi'] ?? 0) <= 50 ? 'Bueno' : 'Moderado' }}
                                        </span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-400 mt-4 border-t border-slate-50 pt-3">
                                    PM2.5: {{ $airQuality['pm2_5'] ?? '--' }} µg/m³ | PM10: {{ $airQuality['pm10'] ?? '--' }} µg/m³
                                </p>
                            </div>
                            
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-2 text-slate-400 text-sm mb-3 font-medium">
                                        <span>☀️</span> <h4>Índice UV</h4>
                                    </div>
                                    <div class="text-3xl font-bold text-slate-800 tracking-tight">
                                        {{ $currentWeather['uv'] }}
                                        <span class="text-sm font-bold ml-2 px-2 py-0.5 rounded {{ $currentWeather['uv'] <= 2 ? 'bg-green-50 text-green-600' : ($currentWeather['uv'] <= 5 ? 'bg-yellow-50 text-yellow-600' : 'bg-red-50 text-red-600') }}">
                                            {{ $currentWeather['uv'] <= 2 ? 'Bajo' : ($currentWeather['uv'] <= 5 ? 'Moderado' : 'Alto') }}
                                        </span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-400 mt-4 border-t border-slate-50 pt-3">
                                    {{ $currentWeather['uv'] >= 6 ? 'Se sugiere usar protector solar.' : 'Condiciones seguras de exposición.' }}
                                </p>
                            </div>

                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 grid grid-cols-2 gap-2 text-center">
                                <div class="border-r border-slate-100 py-1">
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Humedad</p>
                                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $currentWeather['relative_humidity_2m'] }}%</p>
                                </div>
                                <div class="py-1">
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Visibilidad</p>
                                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ round($currentWeather['visibility'] / 1000) }} km</p>
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 grid grid-cols-2 gap-2 text-center">
                                <div class="border-r border-slate-100 py-1">
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Viento</p>
                                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ round($currentWeather['wind_speed_10m']) }} <span class="text-xs font-normal text-slate-400">km/h</span></p>
                                </div>
                                <div class="py-1">
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Presión</p>
                                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ round($currentWeather['surface_pressure']) }} <span class="text-xs font-normal text-slate-400">hPa</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-base font-bold text-slate-800 tracking-tight mb-5">Pronóstico extendido</h3>
                            <div class="space-y-4">
                                @foreach($dailyWeather as $day)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-500 font-medium w-20">{{ $day['dia'] }}</span>
                                        <span class="text-xl select-none" title="{{ $day['desc'] }}">{{ $day['icon'] }}</span>
                                        <span class="text-blue-500 font-semibold text-xs w-10 text-right">
                                            {{ $day['prob_lluvia'] > 0 ? $day['prob_lluvia'] . '%' : '' }}
                                        </span>
                                        <span class="font-bold text-slate-800 w-20 text-right">
                                            {{ $day['max'] }}° <span class="text-slate-400 font-normal">/ {{ $day['min'] }}°</span>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-gradient-to-b from-slate-900 to-slate-800 rounded-2xl shadow-sm p-6 text-white overflow-hidden relative">
                            <div class="absolute -right-4 -bottom-4 text-7xl opacity-5 select-none">🌅</div>
                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Salida / Puesta de Sol</h4>
                            <div class="flex justify-between items-center py-2">
                                <div class="text-center">
                                    <div class="text-2xl mb-1.5">🌅</div>
                                    <div class="font-bold text-sm text-slate-200">{{ $currentWeather['sunrise'] }}</div>
                                </div>
                                <div class="flex-1 px-4">
                                    <div class="h-px bg-gradient-to-r from-transparent via-slate-600 to-transparent"></div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl mb-1.5">🌇</div>
                                    <div class="font-bold text-sm text-slate-200">{{ $currentWeather['sunset'] }}</div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            @else
                <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-100">
                    <p class="text-slate-500">Ocurrió un problema al conectar con las APIs de Open-Meteo. Intenta recargar la página.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>