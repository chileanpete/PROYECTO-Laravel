<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Desafio extends Model
{

    protected $table = 'desafios';
    protected $primaryKey = 'id_desafio';

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo_desafio',
        'categoria',
        'objetivo_valor',
        'unidad_medida',
        'puntos_recompensa',
        'fecha_inicio',
        'fecha_fin',
        'dificultad',
        'activo',
        'icono'
    ];

    /**protected $casts = [
        'objetivo_valor' => 'integer',
        'puntos_recompensa' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'dificultad' => 'integer',
        'activo' => 'boolean'
    ];
*/
    protected $casts = [
        'objetivos_relacionados' => 'array',
    ];
    // Relaciones
    public function usuarioDesafios(): HasMany
    {
        return $this->hasMany(UsuarioDesafio::class, 'id_desafio', 'id_desafio');
    }
}
