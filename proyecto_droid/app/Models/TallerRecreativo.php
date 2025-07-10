<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\InscripcionTaller;

class TallerRecreativo extends Model
{
    protected $table = 'talleres_recreativos';
    protected $primaryKey = 'id_taller';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'instructor',
        'categoria',
        'duracion_minutos',
        'nivel_dificultad',
        'cupo_maximo',
        'costo',
        'ubicacion',
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'imagen_url',
        'requisitos'
    ];

    protected $casts = [
        'duracion_minutos' => 'integer',
        'nivel_dificultad' => 'integer',
        'cupo_maximo' => 'integer',
        'costo' => 'decimal:2',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'activo' => 'boolean'
    ];

    // Relaciones
    public function inscripciones(): HasMany
    {
        return $this->hasMany(InscripcionTaller::class, 'id_taller', 'id_taller');
    }
}
