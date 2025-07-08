<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroConsumo extends Model
{
    protected $table = 'registro_consumo';
    protected $primaryKey = 'id_consumo';
    
    protected $fillable = [
        'id_usuario',
        'id_plato',
        'fecha_consumo',
        'hora_consumo',
        'porciones',
        'calorias_totales',
        'valoracion',
        'comentario',
        'puntos_obtenidos'
    ];

    protected $casts = [
        'fecha_consumo' => 'date',
        'hora_consumo' => 'datetime',
        'porciones' => 'decimal:2',
        'calorias_totales' => 'integer',
        'valoracion' => 'integer',
        'puntos_obtenidos' => 'integer'
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function plato(): BelongsTo
    {
        return $this->belongsTo(Plato::class, 'id_plato', 'id_plato');
    }
}
