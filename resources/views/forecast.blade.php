<x-app-layout>
    <div class="py-8 bg-[#F8FAFC] min-h-screen font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 flex flex-col md:flex-row justify-between md:items-end gap-6">
                <div>
                    <h2 class="text-[28px] font-bold text-slate-800 tracking-tight leading-tight">Pronóstico Detallado</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-slate-500 font-medium">{{ explode(',', $selectedCity->city_name)[0] }}</p>
                        <span class="text-slate-300">•</span>
                        <p class="text-xs text-slate-500 bg-white border border-slate-200 px-2.5 py-1 rounded-full shadow-sm">
                            Actualizado: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
                
                @if($cities->count() > 0)
                    <div class="flex flex-col md:flex-row gap-4 items-center" x-data="{ tab: 'hora' }">
                        <form action="{{ route('forecast') }}" method="GET" class="w-full md:w-auto">
                            <select name="city_id" onchange="this.form.submit()" class="w-full border-slate-200 bg-white rounded-xl shadow-sm text-sm font-semibold text-slate-700 focus:ring-blue-500 focus:border-blue-500 py-2.5 pl-4 pr-10 cursor-pointer">
                                @foreach($cities as $c)
                                    <option value="{{ $c->id }}" {{ $selectedCity->id == $c->id ? 'selected' : '' }}>
                                        {{ explode(',', $c->city_name)[0] }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        <div class="flex bg-slate-200/50 p-1 rounded-xl shadow-inner border border-slate-100 w-full md:w-auto">
                            <button @click="tab = 'hora'" :class="tab === 'hora' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 md:flex-none px-6 py-2 rounded-lg text-sm font-bold transition-all duration-200">Por Hora</button>
                            <button @click="tab = '7dias'" :class="tab === '7dias' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 md:flex-none px-6 py-2 rounded-lg text-sm font-bold transition-all duration-200">7 Días</button>
                            <button @click="tab = '14dias'" :class="tab === '14dias' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 md:flex-none px-6 py-2 rounded-lg text-sm font-bold transition-all duration-200">14 Días</button>
                        </div>
                    </div>
                @endif
            </div>

            @if($current)
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 mb-6">
                    <div class="flex flex-col lg:flex-row justify-between items-center gap-8">
                        
                        <div class="flex items-center gap-8 w-full lg:w-auto">
                            <div class="text-center lg:text-left">
                                <h3 class="text-xl font-bold text-slate-800 capitalize mb-1">{{ \Carbon\Carbon::now()->translatedFormat('l d F') }}</h3>
                                <p class="text-slate-500 font-medium">{{ $current['desc'] }}</p>
                            </div>
                            <div class="flex items-center gap-4 border-l border-slate-100 pl-8">
                                <span class="text-6xl drop-shadow-sm">{{ $current['icon'] }}</span>
                                <span class="text-[80px] font-light text-slate-800 tracking-tighter leading-none">{{ round($current['temperature_2m']) }}<span class="text-4xl text-slate-400 absolute mt-2">°C</span></span>
                            </div>
                        </div>

                        <div class="w-full lg:w-auto bg-slate-50 rounded-2xl p-6 border border-slate-100">
                            <div class="grid grid-cols-2 gap-x-12 gap-y-4 text-sm">
                                <div>
                                    <p class="text-slate-400 font-medium mb-1">Máx / Mín</p>
                                    <p class="font-bold text-slate-800 text-base">{{ $current['max'] }}°C <span class="text-slate-400 font-normal">/ {{ $current['min'] }}°C</span></p>
                                </div>
                                <div>
                                    <p class="text-slate-400 font-medium mb-1">Viento</p>
                                    <p class="font-bold text-slate-800 text-base">{{ round($current['wind_speed_10m']) }} km/h <span class="text-slate-500 font-normal">{{ $current['wind_dir_text'] }}</span></p>
                                </div>
                                <div>
                                    <p class="text-slate-400 font-medium mb-1">Prob. Lluvia</p>
                                    <p class="font-bold text-blue-600 text-base">{{ $current['prob_lluvia'] }}%</p>
                                </div>
                                <div>
                                    <p class="text-slate-400 font-medium mb-1">Humedad</p>
                                    <p class="font-bold text-slate-800 text-base">{{ $current['relative_humidity_2m'] }}%</p>
                                </div>
                                <div class="col-span-2 pt-2 border-t border-slate-200/60 mt-1">
                                    <p class="flex justify-between items-center text-slate-500 font-medium">Precipitación: <span class="text-slate-800 font-bold">{{ $current['lluvia_total'] }} mm</span></p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 mb-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-8 flex items-center gap-2">
                        Temperatura por hora
                    </h3>
                    
                    <div class="h-64 flex items-end justify-between gap-1 pt-8 overflow-x-auto border-b border-slate-100 pb-4 px-2">
                        @foreach($hourly as $hour)
                            @php
                                // Cálculo para altura de la barra en la gráfica
                                $heightPercent = max(15, ($hour['temp'] / max(1, $maxTempHourly)) * 100);
                            @endphp
                            
                            <div class="flex flex-col items-center flex-1 min-w-[55px] h-full justify-end group cursor-pointer">
                                <span class="text-xs font-bold text-blue-500 mb-2 opacity-0 group-hover:opacity-100 transition-opacity absolute -translate-y-8 bg-blue-50 px-2 py-1 rounded-md">{{ $hour['prob'] }}% ☔</span>
                                
                                <span class="text-base font-bold text-slate-700 mb-3 group-hover:-translate-y-1 transition-transform">{{ $hour['temp'] }}°</span>
                                
                                <div class="w-full max-w-[32px] bg-blue-100 group-hover:bg-blue-500 rounded-t-xl transition-all relative overflow-hidden" style="height: {{ $heightPercent }}%;">
                                    <div class="absolute bottom-0 w-full bg-blue-500/20 h-full"></div>
                                </div>
                                
                                <span class="text-sm font-semibold text-slate-400 mt-4">{{ explode(':', $hour['time'])[0] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 flex items-start gap-6">
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl text-3xl">💨</div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Viento</h4>
                            <div class="flex items-baseline gap-2 mb-4">
                                <span class="text-4xl font-light text-slate-800">{{ round($current['wind_speed_10m']) }}</span>
                                <span class="text-lg font-medium text-slate-500">km/h</span>
                            </div>
                            <div class="space-y-2 text-sm text-slate-600">
                                <p class="flex justify-between items-center bg-slate-50 px-3 py-2 rounded-lg"><span>Dirección:</span> <span class="font-bold text-slate-800">{{ $current['wind_dir_text'] }} ({{ $current['wind_direction_10m'] }}°)</span></p>
                                <p class="flex justify-between items-center bg-slate-50 px-3 py-2 rounded-lg"><span>Ráfagas:</span> <span class="font-bold text-slate-800">hasta {{ round($current['wind_speed_10m'] * 1.5) }} km/h</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 flex items-start gap-6">
                        <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl text-3xl">☔</div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Precipitación</h4>
                            <div class="flex items-baseline gap-2 mb-4">
                                <span class="text-4xl font-light text-slate-800">{{ $current['lluvia_total'] }}</span>
                                <span class="text-lg font-medium text-slate-500">mm</span>
                            </div>
                            <div class="space-y-2 text-sm text-slate-600">
                                <p class="flex justify-between items-center bg-slate-50 px-3 py-2 rounded-lg"><span>Probabilidad:</span> <span class="font-bold {{ $current['prob_lluvia'] > 20 ? 'text-blue-600' : 'text-slate-800' }}">{{ $current['prob_lluvia'] }}%</span></p>
                                <p class="flex justify-between items-center bg-slate-50 px-3 py-2 rounded-lg"><span>Horas de lluvia:</span> <span class="font-bold text-slate-800">{{ $current['prob_lluvia'] > 20 ? 'Varias horas' : '0h' }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-slate-100">
                    <p class="text-slate-500">Cargando pronóstico...</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>