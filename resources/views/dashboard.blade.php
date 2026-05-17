<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-blue-900 leading-tight">
            {{ __('Mi Panel del Clima') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-6 rounded-lg shadow-md mb-8 border-t-4 border-blue-600">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Buscar y agregar ubicación</h3>
                <form action="{{ route('city.store') }}" method="POST" class="flex flex-col md:flex-row gap-4 md:items-end">
                    @csrf
                    <div class="flex-1">
                        <input type="text" name="city_name" required placeholder="Ej. Tijuana, Monterrey..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md shadow transition font-semibold">
                        Guardar Ciudad
                    </button>
                </form>
                @error('city_name') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($cities as $city)
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 flex flex-col justify-between" x-data="{ editMode: false }">
                        
                        <div x-show="!editMode">
                            <div class="flex justify-between items-start">
                                <h3 class="text-xl font-bold text-gray-800 leading-tight">{{ $city->city_name }}</h3>
                                <div class="flex gap-3">
                                    <button @click="editMode = true" class="text-gray-400 hover:text-blue-600 transition" title="Editar ciudad">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    
                                    <form action="{{ route('city.destroy', $city) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta ubicación?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition" title="Eliminar ciudad">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Lat: {{ $city->latitude }} | Lon: {{ $city->longitude }}</p>
                        </div>

                        <div x-show="editMode" style="display: none;" class="mb-4">
                            <form action="{{ route('city.update', $city) }}" method="POST" class="flex flex-col gap-3">
                                @csrf @method('PATCH')
                                <input type="text" name="city_name" value="{{ $city->city_name }}" required class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <div class="flex gap-2">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 text-sm rounded transition shadow">Actualizar</button>
                                    <button type="button" @click="editMode = false" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-1.5 text-sm rounded border border-gray-300 transition">Cancelar</button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="mt-6 flex items-center justify-between border-t border-gray-100 pt-4">
                            <div class="flex items-center">
                                <span class="text-3xl font-semibold text-blue-600">--°C</span>
                                <span class="ml-2 text-2xl">☁️</span>
                            </div>
                            <p class="text-xs text-blue-400 font-medium italic">Fase 1: Pendiente API</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 bg-white rounded-lg shadow-inner">
                        <p class="text-gray-400">Aún no has guardado ninguna ciudad en tu panel.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>