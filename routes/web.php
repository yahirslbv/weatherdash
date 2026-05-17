<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

// Redirigir la raíz a la nueva pantalla principal (Home)
Route::get('/', function () {
    return redirect()->route('home');
});

// Todas las rutas de la aplicación protegidas por sesión
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Rutas de las nuevas vistas (Figma)
    Route::get('/home', [WeatherController::class, 'home'])->name('home');
    Route::get('/forecast', [WeatherController::class, 'forecast'])->name('forecast');
    Route::get('/settings', [WeatherController::class, 'settings'])->name('settings');

    // Rutas para "Mis Lugares" (Gestión de ciudades)
    Route::get('/dashboard', [WeatherController::class, 'index'])->name('dashboard'); // Esta es la vista de Mis Lugares
    Route::post('/dashboard/city', [WeatherController::class, 'store'])->name('city.store');
    Route::patch('/dashboard/city/{city}', [WeatherController::class, 'update'])->name('city.update');
    Route::delete('/dashboard/city/{city}', [WeatherController::class, 'destroy'])->name('city.destroy');
});

// Rutas del perfil de usuario (por defecto de Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';