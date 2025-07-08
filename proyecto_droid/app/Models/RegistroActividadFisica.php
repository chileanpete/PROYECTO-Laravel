<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroActividadFisica extends Model
{
    protected $table = 'registro_actividad_fisica';
    protected $primaryKey = 'id_actividad';
    
    protected $fillable = [
        'id_usuario',
        'id_tipo_ejercicio',
        'id_rutina',
        'id_rutina_ejercicio',
        'fecha_actividad',
        'hora_inicio',
        'hora_fin',
        'duracion_minutos',
        'calorias_quemadas',
        'intensidad',
        'comentario',
        'completada',
        'puntos_obtenidos'
    ];

    public function getCompletadaAttribute($value)
    {
        return (bool) $value;
    }

    public function tipoEjercicio()
    {
        return $this->belongsTo(TipoEjercicio::class, 'id_tipo_ejercicio', 'id_tipo_ejercicio');
    }

    public function rutinaEjercicio()
    {
        return $this->belongsTo(RutinaEjercicio::class, 'id_rutina', 'id_rutina');
    }
}
