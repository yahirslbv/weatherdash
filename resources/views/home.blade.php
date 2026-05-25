<x-app-layout>
    <div class="py-8 min-h-screen font-sans text-slate-200">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 flex flex-col sm:flex-row justify-between sm:items-end gap-4">
                <div>
                    <h2 class="text-[28px] font-bold text-white tracking-tight leading-tight">Dashboard</h2>
                    <p class="text-[#829AB1] mt-1 font-medium">Resumen meteorológico de tu ubicación principal</p>
                </div>
                
                @if($cities->count() > 0)
                    <div class="w-full sm:w-auto">
                        <form action="{{ route('home') }}" method="GET">
                            <select name="city_id" onchange="this.form.submit()" class="w-full sm:w-64 border-[#1E2D56] bg-[#15203D] rounded-xl shadow-sm text-sm font-semibold text-white focus:ring-blue-500 focus:border-blue-500 py-2.5 pl-4 pr-10 cursor-pointer">
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
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="lg:col-span-2 flex flex-col gap-6">
                        
                        <div class="bg-gradient-to-br from-blue-600 to-[#1E3A8A] rounded-[24px] shadow-lg p-8 text-white relative overflow-hidden border border-blue-500/20">
                            <div class="absolute -right-8 -top-8 text-[150px] opacity-10 select-none pointer-events-none leading-none">
                                {{ $currentWeather['icon'] }}
                            </div>
                            
                            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start gap-4">
                                <div>
                                    <h3 class="text-3xl font-extrabold tracking-tight">{{ explode(',', $selectedCity->city_name)[0] }}</h3>
                                    <p class="text-blue-200/80 text-lg font-medium mt-1">{{ count(explode(',', $selectedCity->city_name)) > 1 ? trim(explode(',', $selectedCity->city_name)[1]) : '' }}</p>
                                    
                                    <div class="mt-6">
                                        <span class="bg-[#0B132B]/40 text-white backdrop-blur-md px-4 py-1.5 rounded-full text-sm font-semibold tracking-wide border border-white/10 shadow-sm">
                                            {{ $currentWeather['description'] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right mt-4 sm:mt-0">
                                    <div class="text-[80px] font-light leading-none tracking-tighter">{{ round($currentWeather['temperature_2m']) }}<span class="text-5xl font-light text-blue-100">{{ $units['temp'] }}</span></div>
                                </div>
                            </div>
                            
                            <div class="relative z-10 mt-12 flex flex-col sm:flex-row items-start sm:items-end justify-between border-t border-blue-400/20 pt-5 gap-4">
                                <div class="text-sm space-y-1">
                                    <p class="text-blue-200">Sensación térmica: <span class="font-bold text-white">{{ isset($currentWeather['apparent_temperature']) ? round($currentWeather['apparent_temperature']) : round($currentWeather['temperature_2m']) }}{{ $units['temp'] }}</span></p>                                    <p class="text-blue-200">Rango del día: <span class="font-bold text-white">Máx {{ $currentWeather['max'] }}° / Mín {{ $currentWeather['min'] }}°</span></p>
                                </div>
                                <div class="text-xs font-medium text-blue-200/60 bg-black/20 px-3 py-1.5 rounded-lg">
                                    Actualizado hace un momento
                                </div>
                            </div>
                        </div>

                        <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-8">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-white tracking-tight">Pronóstico por hora</h3>
                                <a href="{{ route('forecast') }}" class="text-blue-400 text-sm font-bold hover:text-blue-300 transition">Ver todo →</a>
                            </div>
                            
                            <div class="flex justify-between items-center overflow-x-auto pb-2 gap-4 hide-scrollbar">
                                @foreach($hourlyWeather as $hour)
                                    <div class="flex flex-col items-center min-w-[70px] bg-[#0B132B]/50 py-4 px-2 rounded-2xl border border-[#1E2D56] transition hover:bg-[#1E2D56]/50">
                                        <span class="text-sm text-[#829AB1] font-semibold mb-2">{{ $hour['hora'] }}</span>
                                        <span class="text-3xl mb-2 select-none">{{ $hour['icon'] }}</span>
                                        <span class="font-extrabold text-white text-lg">{{ $hour['temp'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            
                            <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-6 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-2 mb-4">
                                        <span class="text-xl opacity-80">🍃</span> 
                                        <h4 class="text-[#829AB1] font-bold text-sm uppercase tracking-wider">Calidad del Aire</h4>
                                    </div>
                                    <div class="flex items-end gap-3">
                                        <span class="text-4xl font-extrabold text-white">AQI {{ $airQuality['us_aqi'] ?? '45' }}</span>
                                        <span class="mb-1 text-xs font-bold px-2.5 py-1 rounded bg-[#0B132B] text-[#4ADE80] border border-[#4ADE80]/20">
                                            Bueno
                                        </span>
                                    </div>
                                </div>
                                <p class="text-xs text-[#829AB1] mt-5 font-medium border-t border-[#1E2D56] pt-3">
                                    PM2.5: {{ $airQuality['pm2_5'] ?? '--' }} µg/m³ <span class="mx-1">•</span> PM10: {{ $airQuality['pm10'] ?? '--' }} µg/m³
                                </p>
                            </div>
                            
                            <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-6 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-2 mb-4">
                                        <span class="text-xl opacity-80">☀️</span> 
                                        <h4 class="text-[#829AB1] font-bold text-sm uppercase tracking-wider">Índice UV</h4>
                                    </div>
                                    <div class="flex items-end gap-3">
                                        <span class="text-4xl font-extrabold text-white">{{ $currentWeather['uv'] }}</span>
                                        <span class="mb-1 text-xs font-bold px-2.5 py-1 rounded border {{ $currentWeather['uv'] <= 2 ? 'bg-[#0B132B] text-[#4ADE80] border-[#4ADE80]/20' : ($currentWeather['uv'] <= 5 ? 'bg-[#0B132B] text-[#FACC15] border-[#FACC15]/20' : 'bg-[#0B132B] text-[#F87171] border-[#F87171]/20') }}">
                                            {{ $currentWeather['uv'] <= 2 ? 'Bajo' : ($currentWeather['uv'] <= 5 ? 'Moderado' : 'Alto') }}
                                        </span>
                                    </div>
                                </div>
                                <p class="text-xs text-[#829AB1] mt-5 font-medium border-t border-[#1E2D56] pt-3">
                                    {{ $currentWeather['uv'] >= 6 ? 'Protección solar altamente recomendada.' : 'Condiciones seguras para estar al exterior.' }}
                                </p>
                            </div>

                            <div class="col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div class="bg-[#15203D] rounded-[20px] shadow-lg border border-[#1E2D56] p-5 text-center">
                                    <span class="text-2xl block mb-2 opacity-80">💧</span>
                                    <p class="text-xs font-bold text-[#829AB1] uppercase mb-1">Humedad</p>
                                    <p class="text-xl font-extrabold text-white">{{ $currentWeather['relative_humidity_2m'] }}%</p>
                                </div>
                                <div class="bg-[#15203D] rounded-[20px] shadow-lg border border-[#1E2D56] p-5 text-center">
                                    <span class="text-2xl block mb-2 opacity-80">💨</span>
                                    <p class="text-xs font-bold text-[#829AB1] uppercase mb-1">Viento</p>
                                    <p class="text-xl font-extrabold text-white">{{ round($currentWeather['wind_speed_10m']) }} <span class="text-xs font-medium text-[#829AB1]">{{ $units['wind'] }}</span></p>
                                </div>
                                <div class="bg-[#15203D] rounded-[20px] shadow-lg border border-[#1E2D56] p-5 text-center">
                                    <span class="text-2xl block mb-2 opacity-80">👁️</span>
                                    <p class="text-xs font-bold text-[#829AB1] uppercase mb-1">Visib.</p>
                                    <p class="text-xl font-extrabold text-white">{{ $currentWeather['vis_val'] }} <span class="text-xs font-medium text-[#829AB1]">{{ $units['dist'] }}</span></p>
                                </div>
                                <div class="bg-[#15203D] rounded-[20px] shadow-lg border border-[#1E2D56] p-5 text-center">
                                    <span class="text-2xl block mb-2 opacity-80">⏱️</span>
                                    <p class="text-xs font-bold text-[#829AB1] uppercase mb-1">Presión</p>
                                    <p class="text-xl font-extrabold text-white">{{ $currentWeather['press_val'] }} <span class="text-xs font-medium text-[#829AB1]">{{ $units['press'] }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-6">
                        
                        <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-6 sm:p-8">
                            <h3 class="text-lg font-bold text-white tracking-tight mb-6">Pronóstico 7 días</h3>
                            <div class="space-y-5">
                                @foreach($dailyWeather as $day)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-[#829AB1] font-bold w-20">{{ $day['dia'] }}</span>
                                        
                                        <div class="flex items-center gap-3 flex-1 justify-center">
                                            <span class="text-2xl select-none" title="{{ $day['desc'] }}">{{ $day['icon'] }}</span>
                                            <span class="text-blue-400 font-bold text-xs w-10 text-left">
                                                {{ $day['prob_lluvia'] > 0 ? $day['prob_lluvia'] . '%' : '' }}
                                            </span>
                                        </div>

                                        <span class="font-extrabold text-white w-20 text-right">
                                            {{ $day['max'] }}° <span class="text-[#829AB1] font-medium ml-1">/ {{ $day['min'] }}°</span>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-gradient-to-b from-[#0B132B] to-[#15203D] rounded-[24px] shadow-lg p-8 text-white relative overflow-hidden border border-[#1E2D56]">
                            <div class="absolute -right-6 -bottom-6 text-[100px] opacity-[0.03] select-none">🌅</div>
                            
                            <h4 class="text-sm font-bold text-[#829AB1] uppercase tracking-wider mb-6">Salida / Puesta</h4>
                            <div class="flex justify-between items-center relative z-10">
                                <div class="text-center flex-1">
                                    <div class="text-4xl mb-3 drop-shadow-md">🌅</div>
                                    <div class="font-bold text-lg text-white">{{ $currentWeather['sunrise'] }}</div>
                                </div>
                                <div class="w-12 border-t-2 border-[#1E2D56] border-dashed"></div>
                                <div class="text-center flex-1">
                                    <div class="text-4xl mb-3 drop-shadow-md">🌇</div>
                                    <div class="font-bold text-lg text-white">{{ $currentWeather['sunset'] }}</div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            @else
                <div class="bg-[#15203D] rounded-[24px] shadow-lg p-16 text-center border border-[#1E2D56]">
                    <p class="text-[#829AB1] font-medium text-lg">Cargando datos meteorológicos...</p>
                </div>
            @endif

        </div>
    </div>
    
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</x-app-layout>
