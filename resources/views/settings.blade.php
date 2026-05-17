<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Configuración</h2>
                <p class="text-slate-500 mt-1">Personaliza tu experiencia dentro del panel WeatherDash</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-1">
                    <button class="w-full text-left bg-white text-blue-600 font-semibold px-4 py-3 rounded-xl shadow-sm border-l-4 border-blue-500 text-sm">General & Unidades</button>
                    <button class="w-full text-left text-slate-600 hover:bg-white px-4 py-3 rounded-xl text-sm transition">Notificaciones</button>
                    <button class="w-full text-left text-slate-600 hover:bg-white px-4 py-3 rounded-xl text-sm transition">Apariencia</button>
                    <button class="w-full text-left text-slate-600 hover:bg-white px-4 py-3 rounded-xl text-sm transition">API & Datos</button>
                </div>

                <div class="md:col-span-2 space-y-6">
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-blue-600 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-inner">
                                VB
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-lg">{{ Auth::user()->name }}</h3>
                                <p class="text-sm text-slate-400">{{ Auth::user()->email }}</p>
                                <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded font-medium mt-1 inline-block">Plan Estudiantil UABC</span>
                            </div>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="text-sm bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium px-4 py-2 rounded-xl border border-gray-200 transition">
                            Editar
                        </a>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                        <h3 class="text-lg font-bold text-slate-800 border-b border-gray-100 pb-3">Unidades de Medida</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-600">Temperatura</label>
                                <select class="mt-1 block w-full border-gray-200 rounded-xl bg-slate-50 text-slate-800 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option>Celsius (°C)</option>
                                    <option>Fahrenheit (°F)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600">Velocidad del viento</label>
                                <select class="mt-1 block w-full border-gray-200 rounded-xl bg-slate-50 text-slate-800 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option>km/h</option>
                                    <option>m/s</option>
                                    <option>mph</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600">Distancia / Visibilidad</label>
                                <select class="mt-1 block w-full border-gray-200 rounded-xl bg-slate-50 text-slate-800 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option>Kilómetros (km)</option>
                                    <option>Millas (mi)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-600">Presión atmosférica</label>
                                <select class="mt-1 block w-full border-gray-200 rounded-xl bg-slate-50 text-slate-800 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option>Hectopascales (hPa)</option>
                                    <option>Milímetros de mercurio (mmHg)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                        <h3 class="text-lg font-bold text-slate-800 border-b border-gray-100 pb-3">Notificaciones Integradas</h3>
                        
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div class="text-sm">
                                    <p class="font-semibold text-slate-700">Alertas meteorológicas críticas</p>
                                    <p class="text-xs text-slate-400">Recibir avisos automáticos sobre tormentas o eventos extremos.</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div class="text-sm">
                                    <p class="font-semibold text-slate-700">Resumen diario matutino</p>
                                    <p class="text-xs text-slate-400">Notificación con el pronóstico general al comenzar el día.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl shadow transition">
                            Guardar cambios
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>