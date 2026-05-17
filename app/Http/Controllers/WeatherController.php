<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\FavoriteCity;

class WeatherController extends Controller
{
    public function index()
    {
        $cities = Auth::user()->favoriteCities;
        return view('dashboard', compact('cities'));
    }

    public function store(Request $request)
    {
        $request->validate(['city_name' => 'required|string|max:255']);

        return $this->saveCity($request->city_name);
    }

    public function update(Request $request, FavoriteCity $city)
    {
        // Validar que el usuario sea el dueño de este registro
        if ($city->user_id !== Auth::id()) abort(403);

        $request->validate(['city_name' => 'required|string|max:255']);

        // Al editar, volvemos a buscar las coordenadas
        return $this->saveCity($request->city_name, $city);
    }

    public function destroy(FavoriteCity $city)
    {
        if ($city->user_id !== Auth::id()) abort(403);
        
        $city->delete();
        return back()->with('success', 'Ciudad eliminada.');
    }
    public function home()
    {
        return view('home');
    }

    public function forecast()
    {
        return view('forecast');
    }

    public function settings()
    {
        return view('settings');
    }

    // Método privado para evitar repetir la consulta a la API de Open-Meteo
    private function saveCity($name, $cityModel = null)
    {
        $response = Http::get('https://geocoding-api.open-meteo.com/v1/search', [
            'name' => $name,
            'count' => 1,
            'language' => 'es',
            'format' => 'json'
        ]);

        if ($response->successful() && isset($response->json()['results'][0])) {
            $location = $response->json()['results'][0];
            $data = [
                'city_name' => $location['name'] . (isset($location['admin1']) ? ', ' . $location['admin1'] : ''),
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
            ];

            if ($cityModel) {
                $cityModel->update($data);
                $msg = 'Ciudad actualizada correctamente.';
            } else {
                Auth::user()->favoriteCities()->create($data);
                $msg = 'Ciudad agregada correctamente.';
            }

            return back()->with('success', $msg);
        }

        return back()->withErrors(['city_name' => 'No se encontró la ubicación exacta.']);
    }
    
}