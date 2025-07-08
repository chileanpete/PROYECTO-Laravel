<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\Plato;
use App\Models\FavoritoPlato;
use App\Models\RegistroConsumo;
use App\Models\RegistroActividadFisica;
use App\Models\TipoEjercicio;
use App\Models\RutinaEjercicio;
use App\Models\HistorialPesoImc;
use App\Models\ObjetivoAlimentacion;
use App\Models\MenuDiario;
use App\Models\Desafio;
use App\Models\UsuarioDesafio;
use App\Models\EventoAcademico;
use App\Models\UsuarioEvento;
use App\Models\TallerRecreativo;
use App\Models\InscripcionTaller;
use App\Models\Recomendacion;
use Illuminate\Database\Seeder;

class DatosRelacionadosSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener datos existentes
        $usuarios = Usuario::all();
        $platos = Plato::all();
        $tiposEjercicio = TipoEjercicio::all();
        $rutinasEjercicio = RutinaEjercicio::all();
        $desafios = Desafio::all();
        $eventos = EventoAcademico::all();
        $talleres = TallerRecreativo::all();

        // Verificar que existan los datos necesarios
        if ($usuarios->isEmpty() || $platos->isEmpty()) {
            throw new \Exception('Deben existir usuarios y platos antes de crear datos relacionados');
        }

        // Crear favoritos
        foreach ($usuarios as $usuario) {
            $platosFavoritos = $platos->random(rand(2, 4));
            foreach ($platosFavoritos as $plato) {
                FavoritoPlato::create([
                    'id_usuario' => $usuario->id_usuario,
                    'id_plato' => $plato->id_plato,
                    'fecha_agregado' => now()->subDays(rand(1, 30))
                ]);
            }
        }

        // Crear registros de consumo
        foreach ($usuarios as $usuario) {
            for ($i = 0; $i < rand(5, 10); $i++) {
                $plato = $platos->random();
                $fecha = now()->subDays(rand(1, 30));
                
                RegistroConsumo::create([
                    'id_usuario' => $usuario->id_usuario,
                    'id_plato' => $plato->id_plato,
                    'fecha_consumo' => $fecha->toDateString(),
                    'hora_consumo' => $fecha->setTime(rand(6, 22), rand(0, 59), 0),
                    'porciones' => rand(5, 20) / 10, // 0.5 a 2.0
                    'calorias_totales' => $plato->calorias_por_porcion * (rand(5, 20) / 10),
                    'valoracion' => rand(0, 1) ? rand(1, 5) : null,
                    'comentario' => rand(0, 1) ? 'Comentario del usuario' : null,
                    'puntos_obtenidos' => rand(5, 20)
                ]);
            }
        }

        // Crear registros de actividad física
        foreach ($usuarios as $usuario) {
            for ($i = 0; $i < rand(3, 8); $i++) {
                $tipoEjercicio = $tiposEjercicio->random();
                $fecha = now()->subDays(rand(1, 30));
                $duracion = rand(20, 90);
                
                RegistroActividadFisica::create([
                    'id_usuario' => $usuario->id_usuario,
                    'id_tipo_ejercicio' => $tipoEjercicio->id_tipo_ejercicio,
                    'fecha_actividad' => $fecha->toDateString(),
                    'hora_inicio' => $fecha->setTime(rand(6, 22), rand(0, 59), 0),
                    'hora_fin' => $fecha->setTime(rand(6, 22), rand(0, 59), 0),
                    'duracion_minutos' => $duracion,
                    'intensidad' => rand(1, 3), // 1=baja, 2=moderada, 3=alta
                    'calorias_quemadas' => $duracion * rand(3, 8),
                    'comentario' => 'Actividad física registrada',
                    'puntos_obtenidos' => rand(10, 50),
                    'completada' => true
                ]);
            }
        }

        // Crear historial de peso
        foreach ($usuarios as $usuario) {
            $pesoInicial = rand(60, 90);
            for ($i = 0; $i < rand(5, 15); $i++) {
                $fecha = now()->subDays(rand(1, 90));
                $peso = $pesoInicial + (rand(-5, 5) / 10); // Variación de ±0.5 kg
                
                $alturaMetros = $usuario->altura_cm / 100;
                $imc = round($peso / ($alturaMetros * $alturaMetros), 2);
                
                // Calcular categoría IMC
                if ($imc < 18.5) $categoriaImc = 'bajo_peso';
                elseif ($imc < 25) $categoriaImc = 'peso_normal';
                elseif ($imc < 30) $categoriaImc = 'sobrepeso';
                elseif ($imc < 35) $categoriaImc = 'obesidad_grado_1';
                elseif ($imc < 40) $categoriaImc = 'obesidad_grado_2';
                else $categoriaImc = 'obesidad_grado_3';
                
                HistorialPesoImc::create([
                    'id_usuario' => $usuario->id_usuario,
                    'peso_kg' => $peso,
                    'altura_cm' => $usuario->altura_cm,
                    'imc_calculado' => $imc,
                    'categoria_imc' => $categoriaImc,
                    'fecha_registro' => $fecha,
                    'notas' => 'Registro de peso'
                ]);
            }
        }

        // Crear objetivos de alimentación
        foreach ($usuarios as $usuario) {
            $tiposObjetivo = [
                ['tipo' => 'perder_peso', 'valor' => rand(5, 15), 'unidad' => 'kg'],
                ['tipo' => 'mantener_peso', 'valor' => rand(1, 3), 'unidad' => 'kg'],
                ['tipo' => 'ganar_masa', 'valor' => rand(3, 8), 'unidad' => 'kg']
            ];
            $objetivo = $tiposObjetivo[rand(0, 2)];
            
            ObjetivoAlimentacion::create([
                'id_usuario' => $usuario->id_usuario,
                'tipo_objetivo' => $objetivo['tipo'],
                'valor_objetivo' => $objetivo['valor'],
                'unidad' => $objetivo['unidad'],
                'fecha_inicio' => now()->subDays(rand(1, 30))->toDateString(),
                'fecha_fin' => now()->addDays(rand(30, 90))->toDateString(),
                'activo' => true,
                'progreso_actual' => rand(0, $objetivo['valor'])
            ]);
        }

        // Crear menús diarios (comentado - estructura diferente en migración)
        // foreach ($usuarios as $usuario) {
        //     for ($i = 0; $i < rand(3, 7); $i++) {
        //         $plato = $platos->random();
        //         $fecha = now()->addDays(rand(1, 14));
        //         
        //         MenuDiario::create([
        //             'id_usuario' => $usuario->id_usuario,
        //             'id_plato' => $plato->id_plato,
        //             'fecha_menu' => $fecha->toDateString(),
        //             'tipo_comida' => ['desayuno', 'almuerzo', 'cena', 'snack'][rand(0, 3)],
        //             'porcion_planificada' => rand(5, 20) / 10,
        //             'calorias_planificadas' => $plato->calorias_por_porcion * (rand(5, 20) / 10),
        //             'completado' => rand(0, 1)
        //         ]);
        //     }
        // }

        // Crear inscripciones a desafíos
        foreach ($usuarios as $usuario) {
            $desafiosUsuario = $desafios->random(rand(1, 3));
            foreach ($desafiosUsuario as $desafio) {
                $fechaInicio = now()->subDays(rand(1, 30));
                $completado = rand(0, 1);
                
                UsuarioDesafio::create([
                    'id_usuario' => $usuario->id_usuario,
                    'id_desafio' => $desafio->id_desafio,
                    'fecha_inicio' => $fechaInicio->toDateString(),
                    'fecha_fin' => $completado ? $fechaInicio->addDays(rand(1, 30))->toDateString() : null,
                    'progreso_actual' => rand(0, $desafio->objetivo_valor),
                    'estado' => $completado ? 'completado' : ['inscrito', 'en_progreso'][rand(0, 1)],
                    'completado' => $completado,
                    'fecha_completado' => $completado ? now()->subDays(rand(1, 10))->toDateString() : null,
                    'puntos_obtenidos' => $completado ? rand(10, 50) : 0
                ]);
            }
        }

        // Crear inscripciones a eventos
        foreach ($usuarios as $usuario) {
            $eventosUsuario = $eventos->random(rand(1, 2));
            foreach ($eventosUsuario as $evento) {
                $asistio = rand(0, 1);
                UsuarioEvento::create([
                    'id_usuario' => $usuario->id_usuario,
                    'id_evento' => $evento->id_evento,
                    'estado' => $asistio ? 'asistio' : 'inscrito',
                    'fecha_inscripcion' => now()->subDays(rand(1, 30)),
                    'fecha_asistencia' => $asistio ? now()->subDays(rand(1, 10)) : null,
                    'puntos_obtenidos' => $asistio ? rand(10, 50) : 0
                ]);
            }
        }

        // Crear inscripciones a talleres
        foreach ($usuarios as $usuario) {
            $talleresUsuario = $talleres->random(rand(1, 2));
            foreach ($talleresUsuario as $taller) {
                $estado = ['inscrito', 'asistio', 'cancelado'][rand(0, 2)];
                InscripcionTaller::create([
                    'id_usuario' => $usuario->id_usuario,
                    'id_taller' => $taller->id_taller,
                    'fecha_inscripcion' => now()->subDays(rand(1, 30)),
                    'estado' => $estado,
                    'puntos_obtenidos' => $estado === 'asistio' ? rand(10, 50) : 0,
                    'calificacion' => $estado === 'asistio' ? rand(1, 5) : null,
                    'comentario' => rand(0, 1) ? 'Comentario del taller' : null
                ]);
            }
        }

        // Crear recomendaciones
        foreach ($usuarios as $usuario) {
            for ($i = 0; $i < rand(2, 5); $i++) {
                $plato = rand(0, 1) ? $platos->random() : null;
                $rutina = rand(0, 1) ? $rutinasEjercicio->random() : null;
                
                Recomendacion::create([
                    'id_usuario' => $usuario->id_usuario,
                    'tipo_recomendacion' => ['nutricion', 'actividad_fisica', 'salud'][rand(0, 2)],
                    'titulo' => 'Recomendación personalizada',
                    'descripcion' => 'Descripción detallada de la recomendación',
                    'id_plato' => $plato ? $plato->id_plato : null,
                    'id_rutina_ejercicio' => $rutina ? $rutina->id_rutina : null,
                    'prioridad' => rand(1, 5),
                    'fecha_generacion' => now()->subDays(rand(1, 30)),
                    'visto' => rand(0, 1),
                    'aplicado' => rand(0, 1),
                    'fecha_expiracion' => now()->addDays(rand(7, 30))->toDateString()
                ]);
            }
        }
    }
} 