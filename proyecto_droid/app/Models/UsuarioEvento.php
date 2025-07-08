<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsuarioEvento extends Model
{
    protected $table = 'usuario_eventos';
    protected $primaryKey = 'id_usuario_evento';
    
    protected $fillable = [
        'id_usuario',
        'id_evento',
        'fecha_inscripcion',
        'estado',
        'asistio',
        'notas'
    ];

    protected $casts = [
        'fecha_inscripcion' => 'datetime',
        'asistio' => 'boolean'
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function eventoAcademico(): BelongsTo
    {
        return $this->belongsTo(EventoAcademico::class, 'id_evento', 'id_evento');
    }
}
