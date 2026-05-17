<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Dashboard</h2>
                <p class="text-slate-500 mt-1">Resumen meteorológico de tu ubicación principal</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl shadow-lg p-8 text-white relative overflow-hidden">
                        <div class="absolute -right-10 -top-10 text-9xl opacity-20">⛅</div>
                        
                        <div class="relative z-10 flex justify-between items-start">
                            <div>
                                <h3 class="text-3xl font-bold tracking-tight">Tijuana, Baja California</h3>
                                <p class="text-blue-100 text-lg mt-1">Parcialmente Nublado</p>
                            </div>
                            <div class="text-right">
                                <div class="text-6xl font-light">29°C</div>
                            </div>
                        </div>
                        
                        <div class="relative z-10 mt-12 flex items-end justify-between border-t border-blue-400/50 pt-6">
                            <div>
                                <p class="text-blue-100">Sensación térmica: 32°C</p>
                                <p class="text-blue-100 font-medium text-lg">Máx 35° / Mín 22°</p>
                            </div>
                            <div class="text-sm bg-black/20 px-4 py-2 rounded-lg backdrop-blur-sm">
                                Actualizado hace 2 minutos
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Pronóstico por hora</h3>
                            <a href="#" class="text-blue-600 text-sm font-medium hover:underline">Ver todo →</a>
                        </div>
                        
                        <div class="flex justify-between items-center overflow-x-auto pb-2 gap-4">
                            @foreach([
                                ['hora' => 'Ahora', 'temp' => '29°', 'icon' => '⛅'],
                                ['hora' => '15:00', 'temp' => '31°', 'icon' => '☀️'],
                                ['hora' => '16:00', 'temp' => '33°', 'icon' => '☀️'],
                                ['hora' => '17:00', 'temp' => '34°', 'icon' => '☀️'],
                                ['hora' => '18:00', 'temp' => '31°', 'icon' => '⛅'],
                                ['hora' => '19:00', 'temp' => '27°', 'icon' => '☁️'],
                            ] as $item)
                                <div class="flex flex-col items-center min-w-[60px]">
                                    <span class="text-sm text-gray-500 mb-2">{{ $item['hora'] }}</span>
                                    <span class="text-2xl mb-2">{{ $item['icon'] }}</span>
                                    <span class="font-semibold text-gray-800">{{ $item['temp'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-gray-400">🍃</span>
                                <h4 class="text-gray-600 font-medium">Calidad del Aire</h4>
                            </div>
                            <div class="text-3xl font-bold text-gray-800">AQI 45 <span class="text-lg text-green-500 font-medium ml-2">Bueno</span></div>
                            <p class="text-xs text-gray-400 mt-2">PM2.5: 12 µg/m³ | PM10: 18 µg/m³</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-yellow-500">☀️</span>
                                <h4 class="text-gray-600 font-medium">Índice UV</h4>
                            </div>
                            <div class="text-3xl font-bold text-gray-800">8 <span class="text-lg text-red-500 font-medium ml-2">Alto</span></div>
                            <p class="text-xs text-gray-400 mt-2">Protección solar recomendada</p>
                        </div>

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h4 class="text-gray-600 font-medium mb-1">Humedad</h4>
                            <div class="text-2xl font-bold text-gray-800">68%</div>
                        </div>
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h4 class="text-gray-600 font-medium mb-1">Viento</h4>
                            <div class="text-2xl font-bold text-gray-800">18 km/h</div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-6">Pronóstico 7 días</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 w-20">Hoy</span>
                                <span class="text-xl">⛅</span>
                                <span class="text-blue-500 text-sm">68%</span>
                                <span class="font-medium text-gray-800">34° <span class="text-gray-400 font-normal">/ 22°</span></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 w-20">Mañana</span>
                                <span class="text-xl">☀️</span>
                                <span class="text-transparent text-sm">0%</span>
                                <span class="font-medium text-gray-800">36° <span class="text-gray-400 font-normal">/ 24°</span></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 w-20">Miércoles</span>
                                <span class="text-xl">🌧️</span>
                                <span class="text-blue-500 text-sm">85%</span>
                                <span class="font-medium text-gray-800">28° <span class="text-gray-400 font-normal">/ 20°</span></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 w-20">Jueves</span>
                                <span class="text-xl">⛈️</span>
                                <span class="text-blue-500 text-sm">90%</span>
                                <span class="font-medium text-gray-800">25° <span class="text-gray-400 font-normal">/ 19°</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-b from-indigo-900 to-indigo-700 rounded-2xl shadow-sm p-6 text-white">
                        <h4 class="font-medium mb-4 text-indigo-100">Salida / Puesta</h4>
                        <div class="flex justify-between items-center">
                            <div class="text-center">
                                <div class="text-2xl mb-1">🌅</div>
                                <div class="font-bold">06:24 am</div>
                            </div>
                            <div class="w-16 h-px bg-indigo-400/50"></div>
                            <div class="text-center">
                                <div class="text-2xl mb-1">🌇</div>
                                <div class="font-bold">20:11 pm</div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>