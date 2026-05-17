<x-app-layout>
    <div class="py-8 bg-[#0B132B] min-h-screen font-sans text-slate-200">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 flex flex-col md:flex-row justify-between md:items-end gap-6">
                <div>
                    <h2 class="text-[28px] font-bold text-white tracking-tight leading-tight">Pronóstico Detallado</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-[#829AB1] font-medium">{{ explode(',', $selectedCity->city_name)[0] }}</p>
                        <span class="text-slate-600">•</span>
                        <p class="text-xs text-[#829AB1] bg-[#15203D] border border-[#1E2D56] px-2.5 py-1 rounded-full shadow-sm">
                            Actualizado: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
                
                @if($cities->count() > 0)
                    <div class="flex flex-col sm:flex-row gap-4 items-center" x-data="{ tab: 'hora' }">
                        <form action="{{ route('forecast') }}" method="GET" class="w-full sm:w-auto">
                            <select name="city_id" onchange="this.form.submit()" class="w-full sm:w-48 border-[#1E2D56] bg-[#15203D] rounded-xl shadow-sm text-sm font-semibold text-white focus:ring-blue-500 focus:border-blue-500 py-2.5 pl-4 pr-10 cursor-pointer">
                                @foreach($cities as $c)
                                    <option value="{{ $c->id }}" {{ $selectedCity->id == $c->id ? 'selected' : '' }}>
                                        {{ explode(',', $c->city_name)[0] }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        <div class="flex bg-[#15203D] p-1 rounded-xl border border-[#1E2D56] w-full sm:w-auto">
                            <button @click="tab = 'hora'" :class="tab === 'hora' ? 'bg-[#0B132B] text-white border border-[#1E2D56] shadow-md' : 'text-[#829AB1] hover:text-white'" class="flex-1 sm:flex-none px-5 py-1.5 rounded-lg text-xs font-bold transition-all duration-150">Por Hora</button>
                            <button @click="tab = '7dias'" :class="tab === '7dias' ? 'bg-[#0B132B] text-white border border-[#1E2D56] shadow-md' : 'text-[#829AB1] hover:text-white'" class="flex-1 sm:flex-none px-5 py-1.5 rounded-lg text-xs font-bold transition-all duration-150">7 Días</button>
                            <button @click="tab = '14dias'" :class="tab === '14dias' ? 'bg-[#0B132B] text-white border border-[#1E2D56] shadow-md' : 'text-[#829AB1] hover:text-white'" class="flex-1 sm:flex-none px-5 py-1.5 rounded-lg text-xs font-bold transition-all duration-150">14 Días</button>
                        </div>
                    </div>
                @endif
            </div>

            @if($current)
                <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-8 mb-6 relative overflow-hidden">
                    <div class="flex flex-col lg:flex-row justify-between items-stretch gap-8">
                        <div class="flex flex-col sm:flex-row items-center gap-6 lg:w-1/2">
                            <div class="text-center sm:text-left">
                                <span class="text-xs font-bold text-blue-400 uppercase tracking-widest block mb-1">Hoy</span>
                                <h3 class="text-2xl font-extrabold text-white capitalize">{{ \Carbon\Carbon::now()->translatedFormat('l d F') }}</h3>
                                <p class="text-[#829AB1] font-medium mt-1 text-base">{{ $current['desc'] }}</p>
                            </div>
                            <div class="flex items-center gap-4 sm:border-l border-[#1E2D56] sm:pl-6 w-full sm:w-auto justify-center">
                                <span class="text-5xl select-none leading-none">{{ $current['icon'] }}</span>
                                <span class="text-[72px] font-light text-white tracking-tighter leading-none relative">
                                    {{ round($current['temperature_2m']) }}<span class="text-3xl text-[#829AB1] font-normal absolute ml-1 top-2">°C</span>
                                </span>
                            </div>
                        </div>

                        <div class="flex-1 bg-[#0B132B]/40 rounded-2xl p-6 border border-[#1E2D56]">
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 text-sm">
                                <div><p class="text-[#829AB1] font-semibold text-xs uppercase tracking-wider mb-1">Máx / Mín</p><p class="font-bold text-white text-base">{{ $current['max'] }}° <span class="text-[#829AB1] font-normal text-sm">/ {{ $current['min'] }}°</span></p></div>
                                <div><p class="text-[#829AB1] font-semibold text-xs uppercase tracking-wider mb-1">Prob. Lluvia</p><p class="font-bold text-blue-400 text-base">{{ $current['prob_lluvia'] }}%</p></div>
                                <div><p class="text-[#829AB1] font-semibold text-xs uppercase tracking-wider mb-1">Viento</p><p class="font-bold text-white text-base">{{ round($current['wind_speed_10m']) }} km/h <span class="text-xs text-[#829AB1] font-medium">({{ $current['wind_dir_text'] }})</span></p></div>
                                <div><p class="text-[#829AB1] font-semibold text-xs uppercase tracking-wider mb-1">Precipitación</p><p class="font-bold text-white text-base">{{ $current['lluvia_total'] }} mm</p></div>
                                <div><p class="text-[#829AB1] font-semibold text-xs uppercase tracking-wider mb-1">Humedad</p><p class="font-bold text-white text-base">{{ $current['relative_humidity_2m'] }}%</p></div>
                                <div><p class="text-[#829AB1] font-semibold text-xs uppercase tracking-wider mb-1">Condición</p><p class="font-bold text-blue-400 text-sm truncate">Estable</p></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'hora'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
                    
                    <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-8 mb-6 overflow-hidden">
                        <h3 class="text-base font-bold text-white tracking-tight mb-10">Temperatura por hora</h3>
                        @php
                            $count = count($hourly);
                            $minTemp = min(array_column($hourly, 'temp')) - 2;
                            $maxTemp = max(array_column($hourly, 'temp')) + 2;
                            $tempRange = max(1, $maxTemp - $minTemp);
                            $points = [];
                            foreach($hourly as $index => $hour) {
                                $x = ($index / ($count - 1)) * 100;
                                $y = 100 - (($hour['temp'] - $minTemp) / $tempRange) * 100;
                                $points[] = "$x,$y";
                            }
                            $pointsString = implode(' ', $points);
                            $fillPoints = "0,100 $pointsString 100,100";
                        @endphp
                        <div class="overflow-x-auto hide-scrollbar pb-10">
                            <div class="relative w-full min-w-[700px] h-36 mt-4">
                                <svg class="absolute inset-0 w-full h-full overflow-visible" preserveAspectRatio="none" viewBox="0 0 100 100">
                                    <defs>
                                        <linearGradient id="gradientLine" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.3" />
                                            <stop offset="100%" stop-color="#3B82F6" stop-opacity="0" />
                                        </linearGradient>
                                    </defs>
                                    <polygon points="{{ $fillPoints }}" fill="url(#gradientLine)" />
                                    <polyline points="{{ $pointsString }}" fill="none" stroke="#60A5FA" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" />
                                </svg>
                                @foreach($hourly as $index => $hour)
                                    @php
                                        $x = ($index / ($count - 1)) * 100;
                                        $y = 100 - (($hour['temp'] - $minTemp) / $tempRange) * 100;
                                    @endphp
                                    <div class="absolute top-0 bottom-0 flex flex-col items-center group cursor-pointer" style="left: {{ $x }}%; transform: translateX(-50%); width: 40px;">
                                        <span class="absolute font-bold text-blue-300 text-[10px] opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap bg-[#0B132B] px-1.5 py-0.5 rounded border border-[#1E2D56] shadow-lg z-20 pointer-events-none" style="top: calc({{ $y }}% - 50px);">{{ $hour['prob'] }}% ☔</span>
                                        <span class="absolute font-extrabold text-white text-sm transition-transform group-hover:-translate-y-2 pointer-events-none" style="top: calc({{ $y }}% - 25px);">{{ $hour['temp'] }}°</span>
                                        <div class="absolute w-3.5 h-3.5 bg-[#0B132B] border-[2.5px] border-[#60A5FA] rounded-full group-hover:bg-[#60A5FA] group-hover:shadow-[0_0_12px_#60A5FA] transition-all z-10" style="top: calc({{ $y }}% - 7px);"></div>
                                        <div class="absolute bottom-0 w-px bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity -z-10" style="top: {{ $y }}%;"></div>
                                        <span class="absolute -bottom-8 text-xs font-bold text-[#829AB1] pointer-events-none">{{ explode(':', $hour['time'])[0] }}h</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-6 sm:p-8 flex items-start gap-5">
                            <div class="p-4 bg-[#0B132B]/50 border border-[#1E2D56] rounded-2xl text-2xl select-none">💨</div>
                            <div class="flex-1">
                                <h4 class="text-xs font-bold text-[#829AB1] uppercase tracking-wider mb-2">Viento</h4>
                                <div class="flex items-baseline gap-1.5 mb-4">
                                    <span class="text-3xl font-extrabold text-white">{{ round($current['wind_speed_10m']) }}</span>
                                    <span class="text-sm font-semibold text-[#829AB1]">km/h</span>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between items-center bg-[#0B132B]/40 px-3 py-2 rounded-xl border border-[#1E2D56]/50"><span class="text-[#829AB1] font-medium">Dirección</span><span class="font-bold text-white">{{ $current['wind_dir_text'] }} ({{ $current['wind_direction_10m'] }}°)</span></div>
                                    <div class="flex justify-between items-center bg-[#0B132B]/40 px-3 py-2 rounded-xl border border-[#1E2D56]/50"><span class="text-[#829AB1] font-medium">Ráfagas</span><span class="font-bold text-white">Hasta {{ round($current['wind_speed_10m'] * 1.4) }} km/h</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-6 sm:p-8 flex items-start gap-5">
                            <div class="p-4 bg-[#0B132B]/50 border border-[#1E2D56] rounded-2xl text-2xl select-none">☔</div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-[#829AB1] uppercase tracking-wider mb-2">Precipitación</h4>
                                <div class="flex items-baseline gap-1.5 mb-4">
                                    <span class="text-3xl font-extrabold text-white">{{ $current['lluvia_total'] }}</span>
                                    <span class="text-sm font-semibold text-[#829AB1]">mm</span>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between items-center bg-[#0B132B]/40 px-3 py-2 rounded-xl border border-[#1E2D56]/50"><span class="text-[#829AB1] font-medium">Probabilidad</span><span class="font-bold text-blue-400">{{ $current['prob_lluvia'] }}%</span></div>
                                    <div class="flex justify-between items-center bg-[#0B132B]/40 px-3 py-2 rounded-xl border border-[#1E2D56]/50"><span class="text-[#829AB1] font-medium">Horas de lluvia</span><span class="font-bold text-white">{{ $current['prob_lluvia'] > 20 ? 'Previsión de caída' : '0 horas' }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="tab === '7dias'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
                    <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-6 sm:p-8">
                        <h3 class="text-lg font-bold text-white tracking-tight mb-6">Pronóstico a 7 días</h3>
                        <div class="flex flex-col gap-3">
                            @foreach(array_slice($daily, 0, 7) as $day)
                                <div class="flex items-center justify-between bg-[#0B132B]/40 p-4 rounded-xl border border-[#1E2D56]/50 hover:bg-[#1E2D56]/50 transition cursor-default">
                                    <div class="w-24 sm:w-32"><span class="text-white font-bold text-sm sm:text-base">{{ $day['date'] }}</span></div>
                                    <div class="flex items-center gap-3 w-1/3"><span class="text-2xl sm:text-3xl">{{ $day['icon'] }}</span><span class="text-[#829AB1] text-sm hidden sm:block truncate">{{ $day['desc'] }}</span></div>
                                    <div class="w-12 text-center"><span class="text-blue-400 font-bold text-xs">{{ $day['prob'] > 0 ? $day['prob'] . '%' : '' }}</span></div>
                                    <div class="w-20 text-right"><span class="font-extrabold text-white text-base">{{ $day['max'] }}°</span><span class="text-[#829AB1] font-medium text-sm">/ {{ $day['min'] }}°</span></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div x-show="tab === '14dias'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
                    <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-6 sm:p-8">
                        <h3 class="text-lg font-bold text-white tracking-tight mb-6">Pronóstico extendido (14 días)</h3>
                        <div class="flex flex-col gap-3">
                            @foreach($daily as $day)
                                <div class="flex items-center justify-between bg-[#0B132B]/40 p-4 rounded-xl border border-[#1E2D56]/50 hover:bg-[#1E2D56]/50 transition cursor-default">
                                    <div class="w-24 sm:w-32"><span class="text-white font-bold text-sm sm:text-base">{{ $day['date'] }}</span></div>
                                    <div class="flex items-center gap-3 w-1/3"><span class="text-2xl sm:text-3xl">{{ $day['icon'] }}</span><span class="text-[#829AB1] text-sm hidden sm:block truncate">{{ $day['desc'] }}</span></div>
                                    <div class="w-12 text-center"><span class="text-blue-400 font-bold text-xs">{{ $day['prob'] > 0 ? $day['prob'] . '%' : '' }}</span></div>
                                    <div class="w-20 text-right"><span class="font-extrabold text-white text-base">{{ $day['max'] }}°</span><span class="text-[#829AB1] font-medium text-sm">/ {{ $day['min'] }}°</span></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            @else
                <div class="bg-[#15203D] rounded-[24px] shadow-lg p-16 text-center border border-[#1E2D56]">
                    <p class="text-[#829AB1] font-medium text-lg">Cargando pronóstico detallado...</p>
                </div>
            @endif

        </div>
    </div>
    
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</x-app-layout>