<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DesafioController;
use App\Http\Controllers\Api\UsuarioDesafioController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/desafios', [DesafioController::class, 'index']);

Route::get('/usuario/{id}/desafios-sugeridos', [UsuarioDesafioController::class, 'sugeridos']);



Route::get('/desafios', [DesafioController::class, 'index']);
