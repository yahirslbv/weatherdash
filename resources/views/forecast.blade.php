<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 flex flex-col md:flex-row justify-between md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Pronóstico Detallado</h2>
                    <p class="text-slate-500 mt-1">Tijuana, BC • <span class="text-xs bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">Actualizado: Hace unos momentos</span></p>
                </div>
                
                <div class="flex bg-white p-1 rounded-xl shadow-sm border border-gray-100 self-start">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm">Por Hora</button>
                    <button class="text-slate-600 hover:text-slate-900 px-4 py-2 rounded-lg text-sm font-medium transition">7 Días</button>
                    <button class="text-slate-600 hover:text-slate-900 px-4 py-2 rounded-lg text-sm font-medium transition">14 Días</button>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    <div class="space-y-1">
                        <span class="text-sm font-semibold text-blue-600 uppercase tracking-wider">Hoy</span>
                        <h3 class="text-2xl font-bold text-slate-800">Miércoles, 14 Mayo</h3>
                        <p class="text-slate-500">Parcialmente Nublado</p>
                    </div>
                    <div class="flex items-center justify-center md:justify-start gap-4">
                        <span class="text-5xl">🌤️</span>
                        <span class="text-6xl font-light text-slate-800">29°C</span>
                    </div>
                    <div class="text-sm text-slate-600 space-y-2 md:border-l md:border-gray-100 md:pl-6">
                        <p class="flex justify-between"><span>Máxima / Mínima:</span> <span class="font-semibold text-slate-800">35°C / 22°C</span></p>
                        <p class="flex justify-between"><span>Prob. Lluvia:</span> <span class="font-semibold text-blue-600">15%</span></p>
                        <p class="flex justify-between"><span>Humedad:</span> <span class="font-semibold text-slate-800">68%</span></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-lg font-bold text-slate-800 mb-6">Tendencia de Temperatura por hora</h3>
                <div class="h-48 flex items-end justify-between gap-2 pt-6 overflow-x-auto">
                    @foreach([
                        ['h' => '00:00', 't' => '22°', 'h_p' => '40%'],
                        ['h' => '02:00', 't' => '21°', 'h_p' => '35%'],
                        ['h' => '04:00', 't' => '20°', 'h_p' => '30%'],
                        ['h' => '08:00', 't' => '25°', 'h_p' => '55%'],
                        ['h' => '10:00', 't' => '28°', 'h_p' => '70%'],
                        ['h' => '12:00', 't' => '31°', 'h_p' => '85%'],
                        ['h' => '14:00', 't' => '34°', 'h_p' => '95%'],
                        ['h' => '16:00', 't' => '33°', 'h_p' => '90%'],
                        ['h' => '18:00', 't' => '30°', 'h_p' => '75%'],
                        ['h' => '20:00', 't' => '27°', 'h_p' => '65%'],
                        ['h' => '22:00', 't' => '24°', 'h_p' => '50%']
                    ] as $bar)
                        <div class="flex flex-col items-center flex-1 min-w-[45px] h-full justify-end">
                            <span class="text-sm font-semibold text-slate-700 mb-2">{{ $bar['t'] }}</span>
                            <div class="w-full bg-blue-500 hover:bg-blue-600 rounded-t-md transition-all cursor-pointer" style="height: {{ $bar['h_p'] }}"></div>
                            <span class="text-xs text-slate-400 mt-2">{{ $bar['h'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-start gap-4">
                    <div class="p-3 bg-blue-50 rounded-xl text-2xl text-blue-600">💨</div>
                    <div class="flex-1 space-y-2">
                        <h4 class="font-bold text-slate-800">Detalles del Viento</h4>
                        <div class="text-3xl font-light text-slate-800">18 km/h</div>
                        <div class="text-sm text-slate-500 space-y-1">
                            <p>Dirección: <span class="font-medium text-slate-700">Noroeste (NO 315°)</span></p>
                            <p>Ráfagas: <span class="font-medium text-slate-700">Hasta 28 km/h</span></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-start gap-4">
                    <div class="p-3 bg-blue-50 rounded-xl text-2xl text-blue-600">🌧️</div>
                    <div class="flex-1 space-y-2">
                        <h4 class="font-bold text-slate-800">Precipitación</h4>
                        <div class="text-3xl font-light text-slate-800">0.0 mm</div>
                        <div class="text-sm text-slate-500 space-y-1">
                            <p>Probabilidad de lluvia: <span class="font-medium text-slate-700">15%</span></p>
                            <p>Horas estimadas de lluvia: <span class="font-medium text-slate-700">0 h</span></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>