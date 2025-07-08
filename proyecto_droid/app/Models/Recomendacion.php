<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recomendacion extends Model
{
    protected $table = 'recomendaciones';
    protected $primaryKey = 'id_recomendacion';
    
    protected $fillable = [
        'id_usuario',
        'tipo_recomendacion',
        'titulo',
        'descripcion',
        'id_plato',
        'id_rutina_ejercicio',
        'prioridad',
        'fecha_generacion',
        'visto',
        'aplicado',
        'fecha_expiracion'
    ];

    protected $casts = [
        'prioridad' => 'integer',
        'fecha_generacion' => 'datetime',
        'visto' => 'boolean',
        'aplicado' => 'boolean',
        'fecha_expiracion' => 'date'
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function plato(): BelongsTo
    {
        return $this->belongsTo(Plato::class, 'id_plato', 'id_plato');
    }

    public function rutinaEjercicio(): BelongsTo
    {
        return $this->belongsTo(RutinaEjercicio::class, 'id_rutina_ejercicio', 'id_rutina');
    }
}
