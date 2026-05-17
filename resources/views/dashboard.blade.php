<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Mis Lugares</h2>
                <p class="text-slate-500 mt-1">Gestiona y compara tus ubicaciones guardadas</p>
            </div>

            @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-10">
                <form action="{{ route('city.store') }}" method="POST" class="relative max-w-2xl">
                    @csrf
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="city_name" required 
                           placeholder="Buscar ciudad, país o región..." 
                           class="block w-full pl-11 pr-32 py-4 bg-white border-none rounded-xl shadow-sm text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <button type="submit" class="absolute right-2 top-2 bottom-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 rounded-lg transition-colors">
                        + Agregar lugar
                    </button>
                </form>
                @error('city_name') <p class="text-red-500 text-sm mt-2 ml-2">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($cities as $city)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow relative group" x-data="{ editMode: false }">
                        
                        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                            <button @click="editMode = true" class="text-gray-400 hover:text-blue-600 bg-white p-1 rounded-full shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                            <form action="{{ route('city.destroy', $city) }}" method="POST" onsubmit="return confirm('¿Eliminar esta ubicación?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 bg-white p-1 rounded-full shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>

                        <div x-show="!editMode" class="flex flex-col h-full justify-between">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-xl font-bold text-slate-800 leading-tight">{{ explode(',', $city->city_name)[0] }}</h3>
                                    <p class="text-sm text-slate-500 mt-1">{{ count(explode(',', $city->city_name)) > 1 ? trim(explode(',', $city->city_name)[1]) : 'Ubicación guardada' }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-4xl font-light text-slate-800">
                                        {{ isset($city->weather) ? round($city->weather['temperature_2m']) . '°C' : '--°C' }}
                                    </div>
                                    <div class="text-2xl mt-1">
                                        {{ $city->weather['icon'] ?? '🌤️' }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <p class="text-blue-600 font-medium">
                                    {{ $city->weather['description'] ?? 'Conectando a API...' }}
                                </p>
                            </div>

                            <div class="mt-6 flex items-center justify-between border-t border-gray-100 pt-4 text-sm text-slate-600">
                                <div class="flex items-center gap-1">
                                    <span class="text-gray-400">🌡️</span>
                                    <span>Act.</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="text-blue-400">💧</span>
                                    <span>{{ isset($city->weather) ? $city->weather['relative_humidity_2m'] : '--' }}%</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="text-gray-400">💨</span>
                                    <span>{{ isset($city->weather) ? round($city->weather['wind_speed_10m']) : '--' }} km/h</span>
                                </div>
                            </div>
                        </div>

                        <div x-show="editMode" style="display: none;" class="py-2">
                            <form action="{{ route('city.update', $city) }}" method="POST" class="flex flex-col gap-3">
                                @csrf @method('PATCH')
                                <label class="text-sm text-gray-500">Corregir nombre:</label>
                                <input type="text" name="city_name" value="{{ $city->city_name }}" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <div class="flex gap-2 mt-2">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm rounded-lg transition w-full">Guardar</button>
                                    <button type="button" @click="editMode = false" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 text-sm rounded-lg transition w-full">Cancelar</button>
                                </div>
                            </form>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100 border-dashed">
                        <div class="text-5xl mb-4">🌍</div>
                        <h3 class="text-lg font-bold text-gray-800">No hay ubicaciones guardadas</h3>
                        <p class="text-gray-500 mt-2 max-w-md mx-auto">Utiliza la barra de búsqueda superior para encontrar y agregar tu primera ciudad al panel.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>