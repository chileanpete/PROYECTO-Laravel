<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DesafioController;
use App\Http\Controllers\Api\UsuarioDesafioController;
use App\Http\Controllers\RegistroConsumoController;
use App\Http\Controllers\RegistroActividadFisicaController;
use App\Http\Controllers\InscripcionTallerController;
use App\Http\Controllers\PlatoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas de Desafíos
Route::get('/desafios', [DesafioController::class, 'index']);
Route::get('/usuario/{id}/desafios-sugeridos', [UsuarioDesafioController::class, 'sugeridos']);

// Rutas de Registro de Consumo (Alimentación)
Route::prefix('registro-consumo')->group(function () {
    Route::get('/', [RegistroConsumoController::class, 'index']);
    Route::get('/{id}', [RegistroConsumoController::class, 'show']);
    Route::post('/', [RegistroConsumoController::class, 'store']);
    Route::put('/{id}', [RegistroConsumoController::class, 'update']);
    Route::delete('/{id}', [RegistroConsumoController::class, 'destroy']);
    Route::get('/estadisticas', [RegistroConsumoController::class, 'estadisticas']);
    Route::get('/exportar/pdf', [RegistroConsumoController::class, 'exportarPDF']);
});

// Rutas de Registro de Actividad Física
Route::prefix('registro-actividad')->group(function () {
    Route::get('/', [RegistroActividadFisicaController::class, 'index']);
    Route::get('/{id}', [RegistroActividadFisicaController::class, 'show']);
    Route::post('/', [RegistroActividadFisicaController::class, 'store']);
    Route::put('/{id}', [RegistroActividadFisicaController::class, 'update']);
    Route::delete('/{id}', [RegistroActividadFisicaController::class, 'destroy']);
    Route::get('/estadisticas', [RegistroActividadFisicaController::class, 'estadisticas']);
    Route::get('/tipos-ejercicio', [RegistroActividadFisicaController::class, 'tiposEjercicio']);
    Route::get('/rutinas-ejercicio', [RegistroActividadFisicaController::class, 'rutinasEjercicio']);
    Route::get('/exportar/pdf', [RegistroActividadFisicaController::class, 'exportarPDF']);
});

// Rutas de Inscripciones a Talleres
Route::prefix('inscripciones-talleres')->group(function () {
    Route::get('/', [InscripcionTallerController::class, 'index']);
    Route::get('/{id}', [InscripcionTallerController::class, 'show']);
    Route::post('/', [InscripcionTallerController::class, 'store']);
    Route::put('/{id}', [InscripcionTallerController::class, 'update']);
    Route::delete('/{id}', [InscripcionTallerController::class, 'destroy']);
    Route::get('/talleres-disponibles', [InscripcionTallerController::class, 'talleresDisponibles']);
    Route::get('/estadisticas', [InscripcionTallerController::class, 'estadisticas']);
});

// Rutas de Platos
Route::prefix('platos')->group(function () {
    Route::get('/', [PlatoController::class, 'index']);
    Route::get('/{id}', [PlatoController::class, 'show']);
    Route::get('/categorias', [PlatoController::class, 'categorias']);
    Route::get('/lugares', [PlatoController::class, 'lugares']);
    Route::get('/buscar', [PlatoController::class, 'buscar']);
});
