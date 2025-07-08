<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroActividadFisica extends Model
{
    protected $table = 'registro_actividad_fisica';
    protected $primaryKey = 'id_actividad';
    
    protected $fillable = [
        'id_usuario',
        'id_rutina',
        'id_rutina_ejercicio',
        'id_tipo_ejercicio',
        'fecha_actividad',
        'hora_inicio',
        'hora_fin',
        'duracion_minutos',
        'calorias_quemadas',
        'intensidad',
        'comentario',
        'puntos_obtenidos',
        'completada'
    ];

    protected $casts = [
        'fecha_actividad' => 'date',
        'hora_inicio' => 'datetime',
        'hora_fin' => 'datetime',
        'duracion_minutos' => 'integer',
        'calorias_quemadas' => 'integer',
        'intensidad' => 'integer',
        'puntos_obtenidos' => 'integer',
        'completada' => 'boolean'
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function rutina(): BelongsTo
    {
        return $this->belongsTo(RutinaEjercicio::class, 'id_rutina', 'id_rutina');
    }

    public function rutinaEjercicio(): BelongsTo
    {
        return $this->belongsTo(RutinaEjercicios::class, 'id_rutina_ejercicio', 'id_rutina_ejercicio');
    }

    public function tipoEjercicio(): BelongsTo
    {
        return $this->belongsTo(TipoEjercicio::class, 'id_tipo_ejercicio', 'id_tipo_ejercicio');
    }
}
