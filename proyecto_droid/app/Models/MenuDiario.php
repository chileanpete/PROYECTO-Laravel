<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuDiario extends Model
{
    protected $table = 'menu_diario';
    protected $primaryKey = 'id_menu';
    
    protected $fillable = [
        'id_lugar',
        'id_plato',
        'fecha',
        'disponible',
        'stock_estimado',
        'hora_inicio',
        'hora_fin',
        'precio_especial'
    ];

    protected $casts = [
        'fecha' => 'date',
        'disponible' => 'boolean',
        'stock_estimado' => 'integer',
        'hora_inicio' => 'datetime',
        'hora_fin' => 'datetime',
        'precio_especial' => 'decimal:2'
    ];

    // Relaciones
    public function lugar(): BelongsTo
    {
        return $this->belongsTo(LugarComida::class, 'id_lugar', 'id_lugar');
    }

    public function plato(): BelongsTo
    {
        return $this->belongsTo(Plato::class, 'id_plato', 'id_plato');
    }
}
