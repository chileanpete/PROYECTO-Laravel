<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plato extends Model
{
    protected $table = 'platos';
    protected $primaryKey = 'id_plato';
    
    protected $fillable = [
        'id_lugar',
        'id_categoria',
        'nombre',
        'descripcion',
        'precio',
        'calorias_por_porcion',
        'proteinas_g',
        'carbohidratos_g',
        'grasas_g',
        'fibra_g',
        'azucares_g',
        'sodio_mg',
        'disponible',
        'es_vegetariano',
        'es_vegano',
        'sin_gluten',
        'imagen_url',
        'fecha_creacion'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'calorias_por_porcion' => 'integer',
        'proteinas_g' => 'decimal:2',
        'carbohidratos_g' => 'decimal:2',
        'grasas_g' => 'decimal:2',
        'fibra_g' => 'decimal:2',
        'azucares_g' => 'decimal:2',
        'sodio_mg' => 'decimal:2',
        'disponible' => 'boolean',
        'es_vegetariano' => 'boolean',
        'es_vegano' => 'boolean',
        'sin_gluten' => 'boolean',
        'fecha_creacion' => 'datetime'
    ];

    // Relaciones
    public function lugar(): BelongsTo
    {
        return $this->belongsTo(LugarComida::class, 'id_lugar', 'id_lugar');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaComida::class, 'id_categoria', 'id_categoria');
    }

    public function getEsVegetarianoAttribute($value)
    {
        return (bool) $value;
    }
    public function getEsVeganoAttribute($value)
    {
        return (bool) $value;
    }
    public function getSinGlutenAttribute($value)
    {
        return (bool) $value;
    }
    public function favoritos(): HasMany
    {
        return $this->hasMany(FavoritoPlato::class, 'id_plato', 'id_plato');
    }

    public function registrosConsumo(): HasMany
    {
        return $this->hasMany(RegistroConsumo::class, 'id_plato', 'id_plato');
    }

    public function menusDiarios(): HasMany
    {
        return $this->hasMany(MenuDiario::class, 'id_plato', 'id_plato');
    }
}
