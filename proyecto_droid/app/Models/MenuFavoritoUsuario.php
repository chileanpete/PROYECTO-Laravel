<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuFavoritoUsuario extends Model
{
    protected $table = 'menus_favoritos_usuario';
    protected $primaryKey = 'id_menu_favorito';
    
    protected $fillable = [
        'id_usuario',
        'nombre_menu',
        'descripcion',
        'tipo_comida',
        'calorias_totales',
        'activo',
        'veces_usado',
        'fecha_ultimo_uso'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'calorias_totales' => 'decimal:2',
        'veces_usado' => 'integer',
        'fecha_ultimo_uso' => 'datetime'
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function platos(): HasMany
    {
        return $this->hasMany(MenuFavoritoPlato::class, 'id_menu_favorito', 'id_menu_favorito');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorTipoComida($query, $tipo)
    {
        return $query->where('tipo_comida', $tipo);
    }

    public function scopePorUsuario($query, $idUsuario)
    {
        return $query->where('id_usuario', $idUsuario);
    }

    // Métodos auxiliares
    public function calcularCaloriasTotales()
    {
        $this->calorias_totales = $this->platos()
            ->with('plato')
            ->get()
            ->sum(function ($menuPlato) {
                return $menuPlato->calorias_porcion ?? 
                       ($menuPlato->plato->calorias_por_porcion * $menuPlato->cantidad);
            });
        
        $this->save();
        return $this->calorias_totales;
    }

    public function marcarComoUsado()
    {
        $this->increment('veces_usado');
        $this->fecha_ultimo_uso = now();
        $this->save();
    }
} 