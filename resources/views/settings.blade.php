<x-app-layout>
    <div class="py-8 min-h-screen font-sans text-slate-200" 
         x-data="{ 
            activeTab: 'unidades', 
            savedSuccess: false,
            notifCritical: $persist(true),
            notifDaily: $persist(true),
            notifRain: $persist(false),
            theme: '{{ session('pref_theme', 'dark') === 'light' ? 'light' : 'dark' }}',

            triggerSave() {
                this.savedSuccess = true;
                setTimeout(() => this.savedSuccess = false, 3500);
            },

            setTheme(theme) {
                this.theme = theme;
                document.documentElement.dataset.theme = theme;
                document.documentElement.classList.toggle('theme-light', theme === 'light');
                document.documentElement.classList.toggle('theme-dark', theme === 'dark');
            }
         }">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-10">
                <h2 class="text-[28px] font-bold text-white tracking-tight leading-tight">Configuración</h2>
                <p class="text-[#829AB1] mt-1 font-medium">Personaliza tu experiencia en WeatherDash</p>
            </div>

            @if (session('success'))
                <div class="mb-6 bg-[#15203D] border border-[#4ADE80]/30 text-[#4ADE80] px-5 py-3 rounded-xl shadow-lg flex items-center gap-3">
                    <span>✨</span> {{ session('success') }}
                </div>
            @endif

            <div x-show="savedSuccess" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-4"
                 class="mb-6 bg-[#15203D] border border-[#4ADE80]/30 text-[#4ADE80] px-5 py-3 rounded-xl shadow-lg flex items-center gap-3"
                 style="display: none;">
                <span>✨</span> ¡Cambios guardados correctamente!
            </div>

            {{-- ✅ FORM real que hace POST al servidor --}}
            <form action="{{ route('settings.update') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    
                    <div class="lg:col-span-1">
                        <div class="bg-[#15203D] rounded-[24px] border border-[#1E2D56] p-2 space-y-1 sticky top-8">
                            <button type="button" @click="activeTab = 'unidades'" 
                                    :class="activeTab === 'unidades' ? 'bg-[#0B132B] text-blue-400 border border-[#1E2D56] shadow-inner' : 'text-[#829AB1] hover:text-white hover:bg-[#0B132B]/30'"
                                    class="w-full text-left font-bold px-5 py-3 rounded-xl text-sm flex items-center justify-between transition-all duration-150">
                                Unidades & General
                                <div x-show="activeTab === 'unidades'" class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                            </button>
                            
                            <button type="button" @click="activeTab = 'notificaciones'" 
                                    :class="activeTab === 'notificaciones' ? 'bg-[#0B132B] text-blue-400 border border-[#1E2D56] shadow-inner' : 'text-[#829AB1] hover:text-white hover:bg-[#0B132B]/30'"
                                    class="w-full text-left font-bold px-5 py-3 rounded-xl text-sm flex items-center justify-between transition-all duration-150">
                                Notificaciones
                                <div x-show="activeTab === 'notificaciones'" class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                            </button>
                            
                            <button type="button" @click="activeTab = 'apariencia'" 
                                    :class="activeTab === 'apariencia' ? 'bg-[#0B132B] text-blue-400 border border-[#1E2D56] shadow-inner' : 'text-[#829AB1] hover:text-white hover:bg-[#0B132B]/30'"
                                    class="w-full text-left font-bold px-5 py-3 rounded-xl text-sm flex items-center justify-between transition-all duration-150">
                                Apariencia
                                <div x-show="activeTab === 'apariencia'" class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                            </button>
                            
                            <button type="button" @click="activeTab = 'api'" 
                                    :class="activeTab === 'api' ? 'bg-[#0B132B] text-blue-400 border border-[#1E2D56] shadow-inner' : 'text-[#829AB1] hover:text-white hover:bg-[#0B132B]/30'"
                                    class="w-full text-left font-bold px-5 py-3 rounded-xl text-sm flex items-center justify-between transition-all duration-150">
                                API & Datos
                                <div x-show="activeTab === 'api'" class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                            </button>

                            <div class="pt-4 mt-4 border-t border-[#1E2D56]/50">
                                <button type="button" @click="activeTab = 'acerca'" 
                                        :class="activeTab === 'acerca' ? 'bg-[#0B132B] text-blue-400 border border-[#1E2D56] shadow-inner' : 'text-[#829AB1] hover:text-white hover:bg-[#0B132B]/30'"
                                        class="w-full text-left font-bold px-5 py-3 rounded-xl text-sm flex items-center justify-between transition-all duration-150">
                                    Acerca de
                                    <div x-show="activeTab === 'acerca'" class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-3 space-y-6">
                        
                        <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
                            <div class="flex items-center gap-5 w-full sm:w-auto">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center text-white text-2xl font-extrabold shadow-lg shrink-0">
                                    {{ collect(explode(' ', Auth::user()->name))->map(function($n) { return substr($n, 0, 1); })->take(2)->implode('') }}
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-white text-xl">{{ Auth::user()->name }}</h3>
                                    <p class="text-sm text-[#829AB1] font-medium">{{ Auth::user()->email }}</p>
                                    <span class="text-xs bg-[#0B132B] border border-[#1E2D56] text-blue-400 px-3 py-1 rounded-lg font-bold mt-2 inline-block">Plan Estudiantil UABC</span>
                                </div>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="text-sm w-full sm:w-auto text-center bg-[#0B132B] hover:bg-[#1E2D56] text-white font-bold px-6 py-3 rounded-xl border border-[#1E2D56] transition shadow-sm">
                                Editar Perfil
                            </a>
                        </div>

                        {{-- PESTAÑA: Unidades --}}
                        <div x-show="activeTab === 'unidades'" x-transition:enter="transition ease-out duration-150" class="space-y-6">
                            <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-8">
                                <h3 class="text-lg font-bold text-white border-b border-[#1E2D56] pb-4 mb-6">Unidades de Medida</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                                    {{-- ✅ name="pref_temp", values coinciden con el controlador, selected desde sesión --}}
                                    <div>
                                        <label class="block text-xs font-bold text-[#829AB1] uppercase tracking-wider mb-2">Temperatura</label>
                                        <select name="pref_temp" class="w-full border-[#1E2D56] bg-[#0B132B] rounded-xl shadow-inner text-sm font-semibold text-white focus:ring-blue-500 p-3">
                                            <option value="celsius"   {{ session('pref_temp', 'celsius')    === 'celsius'     ? 'selected' : '' }}>Celsius (°C)</option>
                                            <option value="fahrenheit"{{ session('pref_temp', 'celsius')    === 'fahrenheit'  ? 'selected' : '' }}>Fahrenheit (°F)</option>
                                        </select>
                                    </div>

                                    {{-- ✅ name="pref_wind" --}}
                                    <div>
                                        <label class="block text-xs font-bold text-[#829AB1] uppercase tracking-wider mb-2">Velocidad del viento</label>
                                        <select name="pref_wind" class="w-full border-[#1E2D56] bg-[#0B132B] rounded-xl shadow-inner text-sm font-semibold text-white focus:ring-blue-500 p-3">
                                            <option value="kmh" {{ session('pref_wind', 'kmh') === 'kmh' ? 'selected' : '' }}>km/h</option>
                                            <option value="mph" {{ session('pref_wind', 'kmh') === 'mph' ? 'selected' : '' }}>mph</option>
                                            <option value="ms"  {{ session('pref_wind', 'kmh') === 'ms'  ? 'selected' : '' }}>m/s</option>
                                        </select>
                                    </div>

                                    {{-- ✅ name="pref_dist" --}}
                                    <div>
                                        <label class="block text-xs font-bold text-[#829AB1] uppercase tracking-wider mb-2">Distancia / Visibilidad</label>
                                        <select name="pref_dist" class="w-full border-[#1E2D56] bg-[#0B132B] rounded-xl shadow-inner text-sm font-semibold text-white focus:ring-blue-500 p-3">
                                            <option value="km" {{ session('pref_dist', 'km') === 'km' ? 'selected' : '' }}>Kilómetros (km)</option>
                                            <option value="mi" {{ session('pref_dist', 'km') === 'mi' ? 'selected' : '' }}>Millas (mi)</option>
                                        </select>
                                    </div>

                                    {{-- ✅ name="pref_press" --}}
                                    <div>
                                        <label class="block text-xs font-bold text-[#829AB1] uppercase tracking-wider mb-2">Presión atmosférica</label>
                                        <select name="pref_press" class="w-full border-[#1E2D56] bg-[#0B132B] rounded-xl shadow-inner text-sm font-semibold text-white focus:ring-blue-500 p-3">
                                            <option value="hpa"   {{ session('pref_press', 'hpa') === 'hpa'   ? 'selected' : '' }}>Hectopascales (hPa)</option>
                                            <option value="mmhg"  {{ session('pref_press', 'hpa') === 'mmhg'  ? 'selected' : '' }}>Milímetros de mercurio (mmHg)</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- PESTAÑA: Notificaciones (sin cambios funcionales) --}}
                        <div x-show="activeTab === 'notificaciones'" style="display: none;" x-transition:enter="transition ease-out duration-150" class="space-y-6">
                            <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-8">
                                <h3 class="text-lg font-bold text-white border-b border-[#1E2D56] pb-4 mb-6">Preferencias de Notificaciones</h3>
                                <div class="space-y-6">
                                    <label class="flex items-center justify-between cursor-pointer group">
                                        <div>
                                            <p class="font-bold text-white text-base">Alertas meteorológicas críticas</p>
                                            <p class="text-sm text-[#829AB1] font-medium mt-0.5">Recibir avisos de tormentas y eventos extremos.</p>
                                        </div>
                                        <div class="relative">
                                            <input type="checkbox" x-model="notifCritical" class="sr-only peer">
                                            <div class="w-11 h-6 bg-[#0B132B] border border-[#1E2D56] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </div>
                                    </label>
                                    <div class="border-t border-[#1E2D56]/50"></div>
                                    <label class="flex items-center justify-between cursor-pointer group">
                                        <div>
                                            <p class="font-bold text-white text-base">Resumen diario matutino</p>
                                            <p class="text-sm text-[#829AB1] font-medium mt-0.5">Notificación matutina con el pronóstico del día.</p>
                                        </div>
                                        <div class="relative">
                                            <input type="checkbox" x-model="notifDaily" class="sr-only peer">
                                            <div class="w-11 h-6 bg-[#0B132B] border border-[#1E2D56] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </div>
                                    </label>
                                    <div class="border-t border-[#1E2D56]/50"></div>
                                    <label class="flex items-center justify-between cursor-pointer group">
                                        <div>
                                            <p class="font-bold text-white text-base">Alerta de lluvia</p>
                                            <p class="text-sm text-[#829AB1] font-medium mt-0.5">Notificar 1 hora antes de que comience a llover.</p>
                                        </div>
                                        <div class="relative">
                                            <input type="checkbox" x-model="notifRain" class="sr-only peer">
                                            <div class="w-11 h-6 bg-[#0B132B] border border-[#1E2D56] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-[#829AB1] after:border-[#829AB1] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-checked:after:bg-white"></div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- PESTAÑA: Apariencia --}}
                        <div x-show="activeTab === 'apariencia'" style="display: none;" x-transition:enter="transition ease-out duration-150" class="space-y-6">
                            <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-8">
                                <h3 class="text-lg font-bold text-white border-b border-[#1E2D56] pb-4 mb-6">Interfaz y Apariencia</h3>
                                <input type="hidden" name="pref_theme" value="{{ session('pref_theme', 'dark') === 'light' ? 'light' : 'dark' }}" :value="theme">
                                <label for="pref-theme-light" class="flex items-center justify-between cursor-pointer group gap-6">
                                    <div>
                                        <p class="font-bold text-white text-base" x-text="theme === 'light' ? 'Modo claro activo' : 'Modo oscuro activo'"></p>
                                        <p class="text-sm text-[#829AB1] font-medium mt-0.5">Alterna entre la paleta oscura de WeatherDash y una version clara para usar de dia.</p>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <span class="text-xs font-bold text-[#829AB1]" x-text="theme === 'light' ? 'Claro' : 'Oscuro'"></span>
                                        <div class="relative">
                                            <input id="pref-theme-light" type="checkbox" class="sr-only peer" :checked="theme === 'light'" @change="setTheme($event.target.checked ? 'light' : 'dark')">
                                            <div class="h-7 w-12 rounded-full border border-[#1E2D56] bg-[#0B132B] transition peer-checked:bg-blue-600 after:absolute after:left-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-5"></div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- PESTAÑA: API --}}
                        <div x-show="activeTab === 'api'" style="display: none;" x-transition:enter="transition ease-out duration-150" class="space-y-6">
                            <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-8">
                                <h3 class="text-lg font-bold text-white border-b border-[#1E2D56] pb-4 mb-6">Proveedor de Datos</h3>
                                <div class="space-y-4">
                                    <div class="bg-[#0B132B]/50 p-4 rounded-xl border border-[#1E2D56] text-sm">
                                        <p class="font-bold text-white">API Meteorológica Activa</p>
                                        <p class="text-[#829AB1] mt-1">Open-Meteo API (Servicio de pronóstico libre y gratuito para desarrollo académico).</p>
                                    </div>
                                    <div class="bg-[#0B132B]/50 p-4 rounded-xl border border-[#1E2D56] text-sm">
                                        <p class="font-bold text-white">API Complementaria de Contaminación</p>
                                        <p class="text-[#829AB1] mt-1">Open-Meteo Air Quality API (Métricas de AQI, PM2.5 y PM10).</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PESTAÑA: Acerca de --}}
                        <div x-show="activeTab === 'acerca'" style="display: none;" x-transition:enter="transition ease-out duration-150" class="space-y-6">
                            <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-8 text-sm space-y-4">
                                <h3 class="text-lg font-bold text-white border-b border-[#1E2D56] pb-4 mb-2">Acerca del Proyecto</h3>
                                <p class="leading-relaxed text-[#829AB1]">
                                    <strong class="text-white">WeatherDash V1.0</strong> es un panel avanzado de control y visualización meteorológica diseñado bajo altos estándares de UI/UX y maquetado píxel por píxel con Tailwind CSS.
                                </p>
                                <p class="text-xs text-[#829AB1] pt-4 border-t border-[#1E2D56]/50">
                                    Entorno de Desarrollo • Laravel 12 & Alpine.js
                                </p>
                            </div>
                        </div>

                        {{-- ✅ Botón ahora es type="submit" del form real --}}
                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-8 py-3.5 rounded-xl shadow-lg transition-all active:scale-98">
                                Guardar cambios
                            </button>
                        </div>

                    </div>
                </div>

            </form>{{-- fin del form --}}

        </div>
    </div>
</x-app-layout>
