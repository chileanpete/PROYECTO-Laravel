<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialPesoImc extends Model
{
    protected $table = 'historial_peso_imc';
    protected $primaryKey = 'id_historial';
    
    protected $fillable = [
        'id_usuario',
        'peso_kg',
        'altura_cm',
        'imc_calculado',
        'categoria_imc',
        'fecha_registro',
        'notas'
    ];

    protected $casts = [
        'peso_kg' => 'decimal:2',
        'altura_cm' => 'integer',
        'imc_calculado' => 'decimal:2',
        'fecha_registro' => 'datetime'
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
