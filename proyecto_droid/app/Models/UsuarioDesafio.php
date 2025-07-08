<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsuarioDesafio extends Model
{
    protected $table = 'usuario_desafios';
    protected $primaryKey = 'id_usuario_desafio';
    
    protected $fillable = [
        'id_usuario',
        'id_desafio',
        'fecha_inicio',
        'fecha_fin',
        'progreso_actual',
        'estado',
        'puntos_obtenidos',
        'completado'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'progreso_actual' => 'integer',
        'puntos_obtenidos' => 'integer',
        'completado' => 'boolean'
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function desafio(): BelongsTo
    {
        return $this->belongsTo(Desafio::class, 'id_desafio', 'id_desafio');
    }
}
