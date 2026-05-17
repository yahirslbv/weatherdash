<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

// Redirigir la raíz al dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Usar el controlador para el dashboard, protegiéndolo con auth
Route::get('/dashboard', [WeatherController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas protegidas para el Clima
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [WeatherController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/city', [WeatherController::class, 'store'])->name('city.store'); // <-- Nueva ruta
});

// Rutas protegidas para el Clima
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [WeatherController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/city', [WeatherController::class, 'store'])->name('city.store');
    
    // Nuevas rutas para Editar y Eliminar
    Route::patch('/dashboard/city/{city}', [WeatherController::class, 'update'])->name('city.update');
    Route::delete('/dashboard/city/{city}', [WeatherController::class, 'destroy'])->name('city.destroy');
});

require __DIR__.'/auth.php';