<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LugarComida extends Model
{
    protected $table = 'lugares_comida';
    protected $primaryKey = 'id_lugar';
    
    protected $fillable = [
        'nombre',
        'tipo',
        'ubicacion',
        'coordenadas_lat',
        'coordenadas_lng',
        'horario_apertura',
        'horario_cierre',
        'telefono',
        'calificacion_promedio',
        'precio_promedio',
        'activo',
        'imagen_url'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'calificacion_promedio' => 'decimal:2',
        'precio_promedio' => 'decimal:2',
        'coordenadas_lat' => 'decimal:8',
        'coordenadas_lng' => 'decimal:8',
        'horario_apertura' => 'datetime:H:i:s',
        'horario_cierre' => 'datetime:H:i:s'
    ];

    // Relaciones
    public function platos(): HasMany
    {
        return $this->hasMany(Plato::class, 'id_lugar', 'id_lugar');
    }
}
