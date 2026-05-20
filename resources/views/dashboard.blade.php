<x-app-layout>
    <div class="py-8 bg-[#0B132B] min-h-screen font-sans text-slate-200" 
         x-data="{ 
            selectedCities: [], 
            showModal: false,
            city1: null,
            city2: null,
            openCompareModal() {
                if(this.selectedCities.length === 2) {
                    let c1Element = document.getElementById('city-data-' + this.selectedCities[0]);
                    let c2Element = document.getElementById('city-data-' + this.selectedCities[1]);
                    
                    this.city1 = {
                        name: c1Element.dataset.name,
                        temp: c1Element.dataset.temp,
                        icon: c1Element.dataset.icon,
                        desc: c1Element.dataset.desc,
                        hum: c1Element.dataset.hum,
                        wind: c1Element.dataset.wind
                    };
                    
                    this.city2 = {
                        name: c2Element.dataset.name,
                        temp: c2Element.dataset.temp,
                        icon: c2Element.dataset.icon,
                        desc: c2Element.dataset.desc,
                        hum: c2Element.dataset.hum,
                        wind: c2Element.dataset.wind
                    };
                    
                    this.showModal = true;
                }
            }
         }">
         
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative">
            
            <div class="mb-8">
                <h2 class="text-[28px] font-bold text-white tracking-tight leading-tight">Mis Lugares</h2>
                <p class="text-[#829AB1] font-medium mt-1">Gestiona y compara tus ubicaciones guardadas</p>
            </div>

            @if (session('success'))
                <div class="mb-6 bg-[#0B132B] border border-[#4ADE80]/30 text-[#4ADE80] px-4 py-3 rounded-xl shadow-sm flex items-center gap-3">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            <div class="mb-10">
                <form action="{{ route('city.store') }}" method="POST" class="relative max-w-3xl">
                    @csrf
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-[#829AB1]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="city_name" required 
                           placeholder="Buscar ciudad, país o región..." 
                           class="block w-full pl-12 pr-40 py-4 bg-[#15203D] border border-[#1E2D56] rounded-[20px] shadow-lg text-white placeholder-[#829AB1] focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <button type="submit" class="absolute right-2 top-2 bottom-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm px-6 rounded-2xl transition-colors shadow-md">
                        + Agregar lugar
                    </button>
                </form>
                @error('city_name') <p class="text-red-400 text-sm mt-2 ml-2 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-24">
                @forelse ($cities as $city)
                    <div class="bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] p-6 hover:border-blue-500/50 transition-colors relative group" x-data="{ editMode: false }">
                        
                        <div id="city-data-{{ $city->id }}" 
                             data-name="{{ explode(',', $city->city_name)[0] }}"
                             data-temp="{{ isset($city->weather) ? round($city->weather['temperature_2m']) : '--' }}"
                             data-icon="{{ $city->weather['icon'] ?? '🌤️' }}"
                             data-desc="{{ $city->weather['description'] ?? 'Pendiente...' }}"
                             data-hum="{{ isset($city->weather) ? $city->weather['relative_humidity_2m'] : '--' }}"
                             data-wind="{{ isset($city->weather) ? round($city->weather['wind_speed_10m']) : '--' }}"
                             class="hidden"></div>

                        <div class="flex justify-between items-start mb-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" value="{{ $city->id }}" x-model="selectedCities" :disabled="selectedCities.length >= 2 && !selectedCities.includes('{{ $city->id }}')" class="w-5 h-5 rounded border-[#1E2D56] bg-[#0B132B] text-blue-500 focus:ring-blue-500 focus:ring-offset-[#15203D] disabled:opacity-50 disabled:cursor-not-allowed">
                            </label>

                            <div class="opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                                <button @click="editMode = true" class="text-[#829AB1] hover:text-blue-400 bg-[#0B132B] p-1.5 rounded-lg shadow-sm border border-[#1E2D56]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <form action="{{ route('city.destroy', $city) }}" method="POST" onsubmit="return confirm('¿Eliminar esta ubicación?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-[#829AB1] hover:text-red-400 bg-[#0B132B] p-1.5 rounded-lg shadow-sm border border-[#1E2D56]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div x-show="!editMode" class="flex flex-col">
                            <div>
                                <h3 class="text-2xl font-extrabold text-white leading-tight">{{ explode(',', $city->city_name)[0] }}</h3>
                                <p class="text-sm text-[#829AB1] font-medium mt-0.5">{{ count(explode(',', $city->city_name)) > 1 ? trim(explode(',', $city->city_name)[1]) : 'Ubicación guardada' }}</p>
                            </div>
                            
                            <div class="mt-6 flex justify-between items-end">
                                <div>
                                    {{-- ✅ CORRECCIÓN 1: unidad dinámica en temperatura principal --}}
                                    <div class="text-[56px] font-light text-white leading-none tracking-tighter">
                                        {{ isset($city->weather) ? round($city->weather['temperature_2m']) : '--' }}<span class="text-2xl text-[#829AB1] font-normal">{{ $units['temp'] }}</span>
                                    </div>
                                    <p class="text-blue-400 font-bold text-sm mt-2">
                                        {{ $city->weather['description'] ?? 'Pendiente...' }}
                                    </p>
                                </div>
                                <div class="text-6xl drop-shadow-lg mb-2">
                                    {{ $city->weather['icon'] ?? '🌤️' }}
                                </div>
                            </div>

                            <div class="mt-6 grid grid-cols-3 gap-2 border-t border-[#1E2D56] pt-4 text-xs font-bold text-white text-center">
                                {{-- ✅ CORRECCIÓN 2: unidad dinámica en MÁX/MÍN --}}
                                <div class="bg-[#0B132B]/50 py-2 rounded-xl border border-[#1E2D56]/50">
                                    <span class="block text-[#829AB1] mb-0.5">MÁX/MÍN</span>
                                    --{{ $units['temp'] }}/--{{ $units['temp'] }}
                                </div>
                                <div class="bg-[#0B132B]/50 py-2 rounded-xl border border-[#1E2D56]/50">
                                    <span class="block text-[#829AB1] mb-0.5">HUMEDAD</span>
                                    {{ isset($city->weather) ? $city->weather['relative_humidity_2m'] : '--' }}%
                                </div>
                                <div class="bg-[#0B132B]/50 py-2 rounded-xl border border-[#1E2D56]/50">
                                    <span class="block text-[#829AB1] mb-0.5">VIENTO</span>
                                    {{ isset($city->weather) ? round($city->weather['wind_speed_10m']) : '--' }} <span class="text-[10px] text-[#829AB1] font-medium">km/h</span>
                                </div>
                            </div>
                        </div>

                        <div x-show="editMode" style="display: none;" class="py-2">
                            <form action="{{ route('city.update', $city) }}" method="POST" class="flex flex-col gap-3">
                                @csrf @method('PATCH')
                                <label class="text-sm font-bold text-[#829AB1]">Corregir nombre:</label>
                                <input type="text" name="city_name" value="{{ $city->city_name }}" required class="w-full text-sm bg-[#0B132B] text-white border-[#1E2D56] rounded-xl focus:border-blue-500 focus:ring-blue-500">
                                <div class="flex gap-2 mt-2">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-4 py-2 text-sm rounded-xl transition w-full">Guardar</button>
                                    <button type="button" @click="editMode = false" class="bg-[#0B132B] hover:bg-[#1E2D56] border border-[#1E2D56] text-white font-bold px-4 py-2 text-sm rounded-xl transition w-full">Cancelar</button>
                                </div>
                            </form>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full text-center py-20 bg-[#15203D] rounded-[24px] shadow-lg border border-[#1E2D56] border-dashed">
                        <div class="text-6xl mb-4 opacity-50">🌍</div>
                        <h3 class="text-xl font-bold text-white">No hay ubicaciones guardadas</h3>
                        <p class="text-[#829AB1] font-medium mt-2 max-w-md mx-auto">Utiliza la barra de búsqueda superior para encontrar y agregar tu primera ciudad al panel.</p>
                    </div>
                @endforelse
            </div>

            <div class="fixed bottom-8 right-8 z-40 bg-[#15203D] border border-[#1E2D56] shadow-[0_0_40px_rgba(0,0,0,0.5)] rounded-[20px] p-5 w-80 transition-all duration-300 transform"
                 :class="selectedCities.length > 0 ? 'translate-y-0 opacity-100' : 'translate-y-20 opacity-0 pointer-events-none'">
                <p class="text-sm text-[#829AB1] font-medium mb-4 leading-relaxed">
                    Tip: Selecciona dos ciudades y presiona <strong class="text-white">'Comparar'</strong> para ver sus condiciones lado a lado.
                </p>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold bg-[#0B132B] text-blue-400 px-3 py-1.5 rounded-lg border border-[#1E2D56]">
                        <span x-text="selectedCities.length"></span>/2 seleccionadas
                    </span>
                    <button @click="openCompareModal()"
                            class="font-bold text-sm px-5 py-2 rounded-xl transition shadow-md"
                            :class="selectedCities.length === 2 ? 'bg-blue-600 text-white hover:bg-blue-500 hover:scale-105' : 'bg-[#1E2D56] text-[#829AB1] cursor-not-allowed'"
                            :disabled="selectedCities.length !== 2">
                        Comparar
                    </button>
                </div>
            </div>

            <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    
                    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-[#0B132B] bg-opacity-90 backdrop-blur-sm transition-opacity" @click="showModal = false" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-[#15203D] rounded-[24px] border border-[#1E2D56] text-left overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">
                        
                        <div class="px-8 py-6 border-b border-[#1E2D56] flex justify-between items-center bg-[#0B132B]/50">
                            <h3 class="text-xl font-extrabold text-white" id="modal-title">Comparación de Ciudades</h3>
                            <button @click="showModal = false" class="text-[#829AB1] hover:text-white transition bg-[#15203D] border border-[#1E2D56] p-1.5 rounded-lg">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <div class="px-8 py-10" x-if="city1 && city2">
                            <div class="flex flex-col md:flex-row justify-center items-center gap-12 md:gap-8">
                                
                                <div class="flex-1 w-full text-center">
                                    <h4 class="text-2xl font-extrabold text-white mb-2" x-text="city1?.name"></h4>
                                    <p class="text-sm font-bold text-blue-400 mb-8" x-text="city1?.desc"></p>
                                    <div class="text-[80px] leading-none mb-6 drop-shadow-lg" x-text="city1?.icon"></div>
                                    <div class="text-6xl font-light text-white mb-8"><span x-text="city1?.temp"></span><span class="text-3xl text-[#829AB1] font-normal">{{ $units['temp'] }}</span></div>
                                    
                                    <div class="bg-[#0B132B]/40 rounded-xl p-4 border border-[#1E2D56]/50 grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-[10px] font-bold text-[#829AB1] uppercase tracking-wider mb-1">Humedad</p>
                                            <p class="text-lg font-bold text-white"><span x-text="city1?.hum"></span>%</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-[#829AB1] uppercase tracking-wider mb-1">Viento</p>
                                            <p class="text-lg font-bold text-white"><span x-text="city1?.wind"></span> <span class="text-xs font-normal text-[#829AB1]">km/h</span></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="hidden md:flex flex-col items-center justify-center h-full px-4">
                                    <div class="h-32 w-px bg-[#1E2D56]"></div>
                                    <div class="my-4 bg-[#0B132B] border border-[#1E2D56] text-[#829AB1] font-bold text-xs px-3 py-1.5 rounded-full">VS</div>
                                    <div class="h-32 w-px bg-[#1E2D56]"></div>
                                </div>

                                <div class="flex-1 w-full text-center">
                                    <h4 class="text-2xl font-extrabold text-white mb-2" x-text="city2?.name"></h4>
                                    <p class="text-sm font-bold text-blue-400 mb-8" x-text="city2?.desc"></p>
                                    <div class="text-[80px] leading-none mb-6 drop-shadow-lg" x-text="city2?.icon"></div>
                                    <div class="text-6xl font-light text-white mb-8"><span x-text="city2?.temp"></span><span class="text-3xl text-[#829AB1] font-normal">{{ $units['temp'] }}</span></div>
                                    
                                    <div class="bg-[#0B132B]/40 rounded-xl p-4 border border-[#1E2D56]/50 grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-[10px] font-bold text-[#829AB1] uppercase tracking-wider mb-1">Humedad</p>
                                            <p class="text-lg font-bold text-white"><span x-text="city2?.hum"></span>%</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-[#829AB1] uppercase tracking-wider mb-1">Viento</p>
                                            <p class="text-lg font-bold text-white"><span x-text="city2?.wind"></span> <span class="text-xs font-normal text-[#829AB1]">km/h</span></p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        
                        <div class="px-8 py-5 border-t border-[#1E2D56] bg-[#0B132B]/50 flex justify-end">
                            <button @click="showModal = false; selectedCities = []" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-6 rounded-xl transition shadow-md">
                                Cerrar Comparación
                            </button>
                        </div>
                        
                    </div>
                </div>
            </div>
            </div>
    </div>
</x-app-layout>