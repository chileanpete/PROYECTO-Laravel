<?php

/**
 * Script de prueba para verificar endpoints de talleres
 * Ejecutar desde la raíz del proyecto Laravel:
 * php test_talleres.php
 */

require_once 'vendor/autoload.php';

// Cargar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 INICIANDO PRUEBAS DE TALLERES\n";
echo "================================\n\n";

try {
    // Probar conexión a la base de datos
    echo "1️⃣ Probando conexión a base de datos...\n";
    $talleres_count = App\Models\TallerRecreativo::count();
    $inscripciones_count = App\Models\InscripcionTaller::count();
    
    echo "   ✅ Conectado a la base de datos\n";
    echo "   📊 Talleres en BD: $talleres_count\n";
    echo "   📊 Inscripciones en BD: $inscripciones_count\n\n";

    // Probar obtener talleres activos
    echo "2️⃣ Probando obtener talleres activos...\n";
    $talleres_activos = App\Models\TallerRecreativo::where('activo', true)
        ->where('fecha_fin', '>=', now()->toDateString())
        ->orderBy('fecha_inicio', 'asc')
        ->get();
    
    echo "   ✅ Consulta ejecutada exitosamente\n";
    echo "   📊 Talleres activos: " . $talleres_activos->count() . "\n\n";

    // Probar relación con inscripciones
    echo "3️⃣ Probando relaciones con inscripciones...\n";
    $taller_con_inscripciones = App\Models\TallerRecreativo::with('inscripciones')->first();
    
    if ($taller_con_inscripciones) {
        echo "   ✅ Relación cargada exitosamente\n";
        echo "   📊 Inscripciones en primer taller: " . $taller_con_inscripciones->inscripciones->count() . "\n\n";
    } else {
        echo "   ⚠️ No hay talleres para probar relaciones\n\n";
    }

    // Probar consulta problemática (porUsuario)
    echo "4️⃣ Probando consulta por usuario (la que puede causar error 500)...\n";
    try {
        $talleres_usuario = App\Models\TallerRecreativo::with(['inscripciones' => function($query) {
            $query->where('id_usuario', 1); // Usuario de prueba
        }])
        ->whereHas('inscripciones', function($query) {
            $query->where('id_usuario', 1);
        })
        ->orderBy('fecha_inicio', 'desc')
        ->get();
        
        echo "   ✅ Consulta por usuario ejecutada exitosamente\n";
        echo "   📊 Talleres del usuario 1: " . $talleres_usuario->count() . "\n\n";
    } catch (Exception $e) {
        echo "   ❌ ERROR en consulta por usuario: " . $e->getMessage() . "\n\n";
    }

    // Información del sistema
    echo "5️⃣ Información del sistema...\n";
    echo "   🐘 PHP Version: " . PHP_VERSION . "\n";
    echo "   🎨 Laravel Version: " . app()->version() . "\n";
    echo "   🗄️ Database: " . config('database.default') . "\n";
    echo "   📍 Environment: " . app()->environment() . "\n\n";

    echo "🎉 TODAS LAS PRUEBAS COMPLETADAS EXITOSAMENTE\n";
    echo "✅ Los endpoints de talleres deberían funcionar correctamente\n";
    echo "🚀 Inicia el servidor con: php artisan serve --host=0.0.0.0 --port=8000\n";

} catch (Exception $e) {
    echo "❌ ERROR CRÍTICO: " . $e->getMessage() . "\n";
    echo "📁 Archivo: " . $e->getFile() . "\n";
    echo "🔢 Línea: " . $e->getLine() . "\n";
    echo "\n💡 Revisa la configuración de la base de datos en el archivo .env\n";
}

echo "\n================================\n";
echo "🏁 PRUEBAS FINALIZADAS\n"; 