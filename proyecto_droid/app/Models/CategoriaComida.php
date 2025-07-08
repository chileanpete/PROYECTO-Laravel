<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaComida extends Model
{
    protected $table = 'categorias_comida';
    protected $primaryKey = 'id_categoria';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'icono',
        'color_hex'
    ];

    // Relaciones
    public function platos(): HasMany
    {
        return $this->hasMany(Plato::class, 'id_categoria', 'id_categoria');
    }
}
