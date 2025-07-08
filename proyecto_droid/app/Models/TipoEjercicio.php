<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoEjercicio extends Model
{
    protected $table = 'tipos_ejercicio';
    protected $primaryKey = 'id_tipo_ejercicio';
    
    protected $fillable = [
        'nombre',
        'categoria',
        'descripcion',
        'calorias_por_minuto',
        'nivel_dificultad',
        'equipamiento_necesario',
        'icono',
        'instrucciones'
    ];

    protected $casts = [
        'calorias_por_minuto' => 'decimal:2',
        'nivel_dificultad' => 'integer'
    ];

    // Relaciones
    public function registrosActividad(): HasMany
    {
        return $this->hasMany(RegistroActividadFisica::class, 'id_tipo_ejercicio', 'id_tipo_ejercicio');
    }
}
