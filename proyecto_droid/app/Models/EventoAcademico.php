<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventoAcademico extends Model
{
    protected $table = 'eventos_academicos';
    protected $primaryKey = 'id_evento';
    
    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo_evento',
        'fecha_inicio',
        'fecha_fin',
        'ubicacion',
        'organizador',
        'cupos_disponibles',
        'inscripcion_requerida',
        'url_inscripcion',
        'activo',
        'imagen_url'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'cupos_disponibles' => 'integer',
        'inscripcion_requerida' => 'boolean',
        'activo' => 'boolean'
    ];

    // Relaciones
    public function usuarioEventos(): HasMany
    {
        return $this->hasMany(UsuarioEvento::class, 'id_evento', 'id_evento');
    }
}
