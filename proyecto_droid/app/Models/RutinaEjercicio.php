<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RutinaEjercicio extends Model
{
    protected $table = 'rutinas_ejercicio';
    protected $primaryKey = 'id_rutina';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo_rutina',
        'nivel_dificultad',
        'duracion_minutos',
        'calorias_estimadas',
        'creado_por',
        'activa',
        'imagen_url'
    ];

    protected $casts = [
        'nivel_dificultad' => 'integer',
        'duracion_minutos' => 'integer',
        'calorias_estimadas' => 'integer',
        'activa' => 'boolean'
    ];

    // Relaciones
    public function ejercicios(): HasMany
    {
        return $this->hasMany(RutinaEjercicios::class, 'id_rutina', 'id_rutina');
    }

    public function registrosActividad(): HasMany
    {
        return $this->hasMany(RegistroActividadFisica::class, 'id_rutina', 'id_rutina');
    }
}
