<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InscripcionTaller extends Model
{
    protected $table = 'inscripciones_talleres';
    protected $primaryKey = 'id_inscripcion';
    
    protected $fillable = [
        'id_usuario',
        'id_taller',
        'fecha_inscripcion',
        'estado',
        'puntos_obtenidos',
        'calificacion',
        'comentario'
    ];

    protected $casts = [
        'fecha_inscripcion' => 'datetime',
        'puntos_obtenidos' => 'integer',
        'calificacion' => 'integer'
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function taller(): BelongsTo
    {
        return $this->belongsTo(TallerRecreativo::class, 'id_taller', 'id_taller');
    }
}
