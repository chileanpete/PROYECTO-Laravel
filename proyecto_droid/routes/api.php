<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PlatoController;
use App\Http\Controllers\RegistroActividadFisicaController;
use App\Http\Controllers\RegistroConsumoController;
use App\Http\Controllers\FavoritoPlatoController;
use App\Http\Controllers\MenuDiarioController;
use App\Http\Controllers\ObjetivoAlimentacionController;
use App\Http\Controllers\DesafioController;
use App\Http\Controllers\EventoAcademicoController;
use App\Http\Controllers\TallerRecreativoController;
use App\Http\Controllers\RecomendacionController;
use App\Http\Controllers\HistorialPesoImcController;
use App\Http\Controllers\CategoriaComidaController;
use App\Http\Controllers\LugarComidaController;
use App\Http\Controllers\TipoEjercicioController;
use App\Http\Controllers\RutinaEjercicioController;
use App\Http\Controllers\UsuarioDesafioController;
use App\Http\Controllers\UsuarioEventoController;
use App\Http\Controllers\InscripcionTallerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rutas públicas
Route::post('/usuarios/login', [UsuarioController::class, 'login']);
Route::post('/usuarios/registro', [UsuarioController::class, 'store']);
Route::get('/desafios', [DesafioController::class, 'index']);

// Rutas protegidas (requieren autenticación)
Route::middleware('auth:sanctum')->group(function () {
    
    // Usuarios
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UsuarioController::class, 'index']);
        Route::get('/{id}', [UsuarioController::class, 'show']);
        Route::put('/{id}', [UsuarioController::class, 'update']);
        Route::delete('/{id}', [UsuarioController::class, 'destroy']);
        Route::get('/{id}/estadisticas', [UsuarioController::class, 'estadisticas']);
        Route::post('/{id}/cambiar-password', [UsuarioController::class, 'cambiarPassword']);
    });

    // Platos
    Route::prefix('platos')->group(function () {
        Route::get('/', [PlatoController::class, 'index']);
        Route::get('/{id}', [PlatoController::class, 'show']);
        Route::post('/', [PlatoController::class, 'store']);
        Route::put('/{id}', [PlatoController::class, 'update']);
        Route::delete('/{id}', [PlatoController::class, 'destroy']);
        Route::get('/recomendados/{idUsuario}', [PlatoController::class, 'recomendados']);
        Route::get('/buscar', [PlatoController::class, 'buscar']);
        Route::get('/categoria/{idCategoria}', [PlatoController::class, 'porCategoria']);
        Route::get('/lugar/{idLugar}', [PlatoController::class, 'porLugar']);
    });

    // Actividad Física
    Route::prefix('actividad-fisica')->group(function () {
        Route::get('/exportar-pdf', [RegistroActividadFisicaController::class, 'exportarPDF']);
        Route::get('/', [RegistroActividadFisicaController::class, 'index']);
        Route::get('/{id}', [RegistroActividadFisicaController::class, 'show']);
        Route::post('/', [RegistroActividadFisicaController::class, 'store']);
        Route::put('/{id}', [RegistroActividadFisicaController::class, 'update']);
        Route::delete('/{id}', [RegistroActividadFisicaController::class, 'destroy']);
        Route::get('/estadisticas/{idUsuario}', [RegistroActividadFisicaController::class, 'estadisticas']);
        Route::post('/por-fecha/{idUsuario}', [RegistroActividadFisicaController::class, 'porFecha']);
        Route::patch('/{id}/completar', [RegistroActividadFisicaController::class, 'marcarCompletada']);
    });

    // Registro de Consumo
    Route::prefix('registro-consumo')->group(function () {
        Route::get('/', [RegistroConsumoController::class, 'index']);
        Route::get('/{id}', [RegistroConsumoController::class, 'show']);
        Route::post('/', [RegistroConsumoController::class, 'store']);
        Route::put('/{id}', [RegistroConsumoController::class, 'update']);
        Route::delete('/{id}', [RegistroConsumoController::class, 'destroy']);
        Route::get('/estadisticas/{idUsuario}', [RegistroConsumoController::class, 'estadisticas']);
        Route::get('/por-fecha/{idUsuario}', [RegistroConsumoController::class, 'porFecha']);
    });

    // Favoritos
    Route::prefix('favoritos')->group(function () {
        Route::get('/usuario/{idUsuario}', [FavoritoPlatoController::class, 'porUsuario']);
        Route::post('/', [FavoritoPlatoController::class, 'store']);
        Route::delete('/{id}', [FavoritoPlatoController::class, 'destroy']);
        Route::get('/verificar/{idUsuario}/{idPlato}', [FavoritoPlatoController::class, 'verificarFavorito']);
    });

    // Menús Diarios
    Route::prefix('menus-diarios')->group(function () {
        Route::get('/usuario/{idUsuario}', [MenuDiarioController::class, 'porUsuario']);
        Route::get('/{id}', [MenuDiarioController::class, 'show']);
        Route::post('/', [MenuDiarioController::class, 'store']);
        Route::put('/{id}', [MenuDiarioController::class, 'update']);
        Route::delete('/{id}', [MenuDiarioController::class, 'destroy']);
        Route::post('/por-fecha/{idUsuario}', [MenuDiarioController::class, 'porFecha']);
        Route::patch('/{id}/completar', [MenuDiarioController::class, 'marcarCompletado']);
        Route::post('/resumen-semanal/{idUsuario}', [MenuDiarioController::class, 'resumenSemanal']);
    });

    // Objetivos de Alimentación
    Route::prefix('objetivos-alimentacion')->group(function () {
        Route::get('/usuario/{idUsuario}', [ObjetivoAlimentacionController::class, 'porUsuario']);
        Route::get('/{id}', [ObjetivoAlimentacionController::class, 'show']);
        Route::post('/', [ObjetivoAlimentacionController::class, 'store']);
        Route::put('/{id}', [ObjetivoAlimentacionController::class, 'update']);
        Route::delete('/{id}', [ObjetivoAlimentacionController::class, 'destroy']);
        Route::patch('/{id}/completar', [ObjetivoAlimentacionController::class, 'marcarCompletado']);
        Route::patch('/{id}/progreso', [ObjetivoAlimentacionController::class, 'actualizarProgreso']);
        Route::get('/activos/{idUsuario}', [ObjetivoAlimentacionController::class, 'activos']);
        Route::get('/estadisticas/{idUsuario}', [ObjetivoAlimentacionController::class, 'estadisticas']);
    });

    // Desafíos
    Route::prefix('desafios')->group(function () {
        // Route::get('/', [DesafioController::class, 'index']); // <-- COMENTADA PARA EVITAR CONFLICTO
        Route::get('/{id}', [DesafioController::class, 'show']);
        Route::post('/', [DesafioController::class, 'store']);
        Route::put('/{id}', [DesafioController::class, 'update']);
        Route::delete('/{id}', [DesafioController::class, 'destroy']);
        Route::get('/activos', [DesafioController::class, 'activos']);
        Route::get('/usuario/{idUsuario}', [DesafioController::class, 'porUsuario']);
        Route::get('/tipo/{tipo}', [DesafioController::class, 'porTipo']);
        Route::get('/dificultad/{dificultad}', [DesafioController::class, 'porDificultad']);
        Route::get('/estadisticas', [DesafioController::class, 'estadisticas']);
    });

    // Eventos Académicos
    Route::prefix('eventos-academicos')->group(function () {
        Route::get('/', [EventoAcademicoController::class, 'index']);
        Route::get('/{id}', [EventoAcademicoController::class, 'show']);
        Route::post('/', [EventoAcademicoController::class, 'store']);
        Route::put('/{id}', [EventoAcademicoController::class, 'update']);
        Route::delete('/{id}', [EventoAcademicoController::class, 'destroy']);
        Route::get('/activos', [EventoAcademicoController::class, 'activos']);
        Route::get('/tipo/{tipo}', [EventoAcademicoController::class, 'porTipo']);
        Route::get('/ponente/{ponente}', [EventoAcademicoController::class, 'porPonente']);
        Route::get('/con-cupos', [EventoAcademicoController::class, 'conCupos']);
        Route::get('/usuario/{idUsuario}', [EventoAcademicoController::class, 'porUsuario']);
        Route::get('/estadisticas', [EventoAcademicoController::class, 'estadisticas']);
    });

    // Talleres Recreativos
    Route::prefix('talleres')->group(function () {
        Route::get('/', [TallerRecreativoController::class, 'index']);
        Route::get('/{id}', [TallerRecreativoController::class, 'show']);
        Route::post('/', [TallerRecreativoController::class, 'store']);
        Route::put('/{id}', [TallerRecreativoController::class, 'update']);
        Route::delete('/{id}', [TallerRecreativoController::class, 'destroy']);
        Route::get('/activos', [TallerRecreativoController::class, 'activos']);
        Route::get('/tipo/{tipo}', [TallerRecreativoController::class, 'porTipo']);
        Route::get('/instructor/{instructor}', [TallerRecreativoController::class, 'porInstructor']);
        Route::get('/con-cupos', [TallerRecreativoController::class, 'conCupos']);
        Route::get('/gratuitos', [TallerRecreativoController::class, 'gratuitos']);
        Route::get('/usuario/{idUsuario}', [TallerRecreativoController::class, 'porUsuario']);
        Route::get('/estadisticas', [TallerRecreativoController::class, 'estadisticas']);
    });

    // Recomendaciones
    Route::prefix('recomendaciones')->group(function () {
        Route::get('/usuario/{idUsuario}', [RecomendacionController::class, 'porUsuario']);
        Route::get('/{id}', [RecomendacionController::class, 'show']);
        Route::post('/', [RecomendacionController::class, 'store']);
        Route::put('/{id}', [RecomendacionController::class, 'update']);
        Route::delete('/{id}', [RecomendacionController::class, 'destroy']);
        Route::patch('/{id}/leer', [RecomendacionController::class, 'marcarLeida']);
        Route::get('/no-leidas/{idUsuario}', [RecomendacionController::class, 'noLeidas']);
        Route::get('/tipo/{idUsuario}/{tipo}', [RecomendacionController::class, 'porTipo']);
        Route::get('/prioridad/{idUsuario}/{prioridad}', [RecomendacionController::class, 'porPrioridad']);
        Route::get('/recientes/{idUsuario}', [RecomendacionController::class, 'recientes']);
        Route::get('/estadisticas/{idUsuario}', [RecomendacionController::class, 'estadisticas']);
    });

    // Historial de Peso e IMC
    Route::prefix('historial-peso')->group(function () {
        Route::get('/usuario/{idUsuario}', [HistorialPesoImcController::class, 'porUsuario']);
        Route::get('/{id}', [HistorialPesoImcController::class, 'show']);
        Route::post('/', [HistorialPesoImcController::class, 'store']);
        Route::put('/{id}', [HistorialPesoImcController::class, 'update']);
        Route::delete('/{id}', [HistorialPesoImcController::class, 'destroy']);
        Route::get('/ultimo/{idUsuario}', [HistorialPesoImcController::class, 'ultimoRegistro']);
        Route::post('/rango-fechas/{idUsuario}', [HistorialPesoImcController::class, 'porRangoFechas']);
        Route::get('/estadisticas/{idUsuario}', [HistorialPesoImcController::class, 'estadisticas']);
        Route::get('/progreso/{idUsuario}', [HistorialPesoImcController::class, 'progresoPeso']);
    });

    // Categorías de Comida
    Route::prefix('categorias-comida')->group(function () {
        Route::get('/', [CategoriaComidaController::class, 'index']);
        Route::get('/{id}', [CategoriaComidaController::class, 'show']);
        Route::post('/', [CategoriaComidaController::class, 'store']);
        Route::put('/{id}', [CategoriaComidaController::class, 'update']);
        Route::delete('/{id}', [CategoriaComidaController::class, 'destroy']);
    });

    // Lugares de Comida
    Route::prefix('lugares-comida')->group(function () {
        Route::get('/', [LugarComidaController::class, 'index']);
        Route::get('/{id}', [LugarComidaController::class, 'show']);
        Route::post('/', [LugarComidaController::class, 'store']);
        Route::put('/{id}', [LugarComidaController::class, 'update']);
        Route::delete('/{id}', [LugarComidaController::class, 'destroy']);
    });

    // Tipos de Ejercicio
    Route::prefix('tipos-ejercicio')->group(function () {
        Route::get('/', [TipoEjercicioController::class, 'index']);
        Route::get('/{id}', [TipoEjercicioController::class, 'show']);
        Route::post('/', [TipoEjercicioController::class, 'store']);
        Route::put('/{id}', [TipoEjercicioController::class, 'update']);
        Route::delete('/{id}', [TipoEjercicioController::class, 'destroy']);
    });

    // Rutinas de Ejercicio
    Route::prefix('rutinas-ejercicio')->group(function () {
        Route::get('/', [RutinaEjercicioController::class, 'index']);
        Route::get('/{id}', [RutinaEjercicioController::class, 'show']);
        Route::post('/', [RutinaEjercicioController::class, 'store']);
        Route::put('/{id}', [RutinaEjercicioController::class, 'update']);
        Route::delete('/{id}', [RutinaEjercicioController::class, 'destroy']);
        Route::get('/activas', [RutinaEjercicioController::class, 'activas']);
        Route::get('/tipo/{tipo}', [RutinaEjercicioController::class, 'porTipo']);
        Route::get('/dificultad/{nivel}', [RutinaEjercicioController::class, 'porDificultad']);
        Route::post('/por-duracion', [RutinaEjercicioController::class, 'porDuracion']);
        Route::get('/populares', [RutinaEjercicioController::class, 'populares']);
        Route::get('/estadisticas', [RutinaEjercicioController::class, 'estadisticas']);
    });

    // Usuario Desafíos
    Route::prefix('usuario-desafios')->group(function () {
        Route::get('/usuario/{idUsuario}', [UsuarioDesafioController::class, 'porUsuario']);
        Route::get('/{id}', [UsuarioDesafioController::class, 'show']);
        Route::post('/', [UsuarioDesafioController::class, 'store']);
        Route::put('/{id}', [UsuarioDesafioController::class, 'update']);
        Route::delete('/{id}', [UsuarioDesafioController::class, 'destroy']);
        Route::get('/activos/{idUsuario}', [UsuarioDesafioController::class, 'activos']);
        Route::get('/completados/{idUsuario}', [UsuarioDesafioController::class, 'completados']);
        Route::get('/vencidos/{idUsuario}', [UsuarioDesafioController::class, 'vencidos']);
        Route::patch('/{id}/completar', [UsuarioDesafioController::class, 'marcarCompletado']);
        Route::get('/estadisticas/{idUsuario}', [UsuarioDesafioController::class, 'estadisticas']);
    });

    // Usuario Eventos
    Route::prefix('usuario-eventos')->group(function () {
        Route::get('/usuario/{idUsuario}', [UsuarioEventoController::class, 'porUsuario']);
        Route::get('/{id}', [UsuarioEventoController::class, 'show']);
        Route::post('/', [UsuarioEventoController::class, 'store']);
        Route::put('/{id}', [UsuarioEventoController::class, 'update']);
        Route::delete('/{id}', [UsuarioEventoController::class, 'destroy']);
        Route::patch('/{id}/asistencia', [UsuarioEventoController::class, 'marcarAsistencia']);
        Route::get('/futuros/{idUsuario}', [UsuarioEventoController::class, 'futuros']);
        Route::get('/pasados/{idUsuario}', [UsuarioEventoController::class, 'pasados']);
        Route::get('/asistidos/{idUsuario}', [UsuarioEventoController::class, 'asistidos']);
        Route::get('/cancelados/{idUsuario}', [UsuarioEventoController::class, 'cancelados']);
        Route::get('/estadisticas/{idUsuario}', [UsuarioEventoController::class, 'estadisticas']);
    });

    // Inscripciones Talleres
    Route::prefix('inscripciones-talleres')->group(function () {
        Route::get('/usuario/{idUsuario}', [InscripcionTallerController::class, 'porUsuario']);
        Route::get('/{id}', [InscripcionTallerController::class, 'show']);
        Route::post('/', [InscripcionTallerController::class, 'store']);
        Route::put('/{id}', [InscripcionTallerController::class, 'update']);
        Route::delete('/{id}', [InscripcionTallerController::class, 'destroy']);
        Route::patch('/{id}/asistencia', [InscripcionTallerController::class, 'marcarAsistencia']);
        Route::patch('/{id}/calificar', [InscripcionTallerController::class, 'calificar']);
        Route::get('/futuros/{idUsuario}', [InscripcionTallerController::class, 'futuros']);
        Route::get('/pasados/{idUsuario}', [InscripcionTallerController::class, 'pasados']);
        Route::get('/asistidos/{idUsuario}', [InscripcionTallerController::class, 'asistidos']);
        Route::get('/cancelados/{idUsuario}', [InscripcionTallerController::class, 'cancelados']);
        Route::get('/estadisticas/{idUsuario}', [InscripcionTallerController::class, 'estadisticas']);
    });

    // Exportación de Datos
    Route::prefix('exportacion')->group(function () {
        Route::get('/actividad-pdf', [\App\Http\Controllers\ExportacionDatosController::class, 'exportarActividadPDF']);
        Route::get('/consumo-pdf', [\App\Http\Controllers\ExportacionDatosController::class, 'exportarConsumoPDF']);
    });

    // Usuario actual
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Ruta de prueba pública
Route::get('/test', function () {
    return response()->json([
        'message' => 'API funcionando correctamente',
        'timestamp' => now()
    ]);
});

// Ruta de prueba autenticada
Route::get('/test-auth', function () {
    return response()->json([
        'message' => 'Autenticación exitosa',
        'user' => auth()->user(),
        'timestamp' => now()
    ]);
})->middleware('auth:sanctum');

// Ruta de prueba con token específico
Route::get('/test-token', function () {
    return response()->json([
        'message' => 'Token verificado correctamente',
        'token' => request()->bearerToken(),
        'timestamp' => now()
    ]);
})->middleware('verify.api.token');
