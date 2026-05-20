<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

// Redirigir la raíz al Dashboard (Home)
Route::get('/', function () {
    return redirect()->route('home');
});

// Todas las rutas de la aplicación protegidas por sesión
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Vistas principales (Figma)
    Route::get('/home', [WeatherController::class, 'home'])->name('home');
    Route::get('/forecast', [WeatherController::class, 'forecast'])->name('forecast');
    Route::get('/settings', [WeatherController::class, 'settings'])->name('settings');
    Route::post('/settings', [WeatherController::class, 'updateSettings'])->name('settings.update'); // Movida aquí

    // "Mis Lugares" (Gestión de ciudades)
    Route::get('/dashboard', [WeatherController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/city', [WeatherController::class, 'store'])->name('city.store');
    Route::patch('/dashboard/city/{city}', [WeatherController::class, 'update'])->name('city.update');
    Route::delete('/dashboard/city/{city}', [WeatherController::class, 'destroy'])->name('city.destroy');
});

// Rutas de perfil (Laravel Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';