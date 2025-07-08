<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RutinaEjercicios extends Model
{
    protected $table = 'rutina_ejercicios';
    protected $primaryKey = 'id_rutina_ejercicio';
    
    protected $fillable = [
        'id_rutina',
        'id_tipo_ejercicio',
        'orden',
        'duracion_minutos',
        'series',
        'repeticiones',
        'peso_kg',
        'descanso_segundos',
        'notas'
    ];

    protected $casts = [
        'orden' => 'integer',
        'duracion_minutos' => 'integer',
        'series' => 'integer',
        'repeticiones' => 'integer',
        'peso_kg' => 'decimal:2',
        'descanso_segundos' => 'integer'
    ];

    // Relaciones
    public function rutina(): BelongsTo
    {
        return $this->belongsTo(RutinaEjercicio::class, 'id_rutina', 'id_rutina');
    }

    public function tipoEjercicio(): BelongsTo
    {
        return $this->belongsTo(TipoEjercicio::class, 'id_tipo_ejercicio', 'id_tipo_ejercicio');
    }

    public function registrosActividad(): BelongsTo
    {
        return $this->belongsTo(RegistroActividadFisica::class, 'id_rutina_ejercicio', 'id_rutina_ejercicio');
    }
}
