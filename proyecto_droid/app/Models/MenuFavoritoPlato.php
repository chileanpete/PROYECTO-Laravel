<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuFavoritoPlato extends Model
{
    protected $table = 'menu_favorito_platos';
    protected $primaryKey = 'id_menu_plato';
    
    protected $fillable = [
        'id_menu_favorito',
        'id_plato',
        'cantidad',
        'unidad',
        'calorias_porcion',
        'notas',
        'orden'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'calorias_porcion' => 'decimal:2',
        'orden' => 'integer'
    ];

    // Relaciones
    public function menuFavorito(): BelongsTo
    {
        return $this->belongsTo(MenuFavoritoUsuario::class, 'id_menu_favorito', 'id_menu_favorito');
    }

    public function plato(): BelongsTo
    {
        return $this->belongsTo(Plato::class, 'id_plato', 'id_plato');
    }

    // Scopes
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }

    // Métodos auxiliares
    public function calcularCalorias()
    {
        if ($this->calorias_porcion) {
            return $this->calorias_porcion;
        }
        
        if ($this->plato && $this->plato->calorias_por_porcion) {
            return $this->plato->calorias_por_porcion * $this->cantidad;
        }
        
        return 0;
    }
} 