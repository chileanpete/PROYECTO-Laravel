<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObjetivoAlimentacion extends Model
{
    protected $table = 'objetivos_alimentacion';
    protected $primaryKey = 'id_objetivo';
    
    protected $fillable = [
        'id_usuario',
        'tipo_objetivo',
        'valor_objetivo',
        'unidad',
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'progreso_actual'
    ];

    protected $casts = [
        'valor_objetivo' => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
        'progreso_actual' => 'decimal:2'
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
