<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroConsumo extends Model
{
    protected $table = 'registro_consumo';
    protected $primaryKey = 'id_consumo';
    protected $fillable = [
        'id_usuario',
        'id_plato',
        'fecha_consumo',
        'hora_consumo',
        'porciones',
        'valoracion',
        'comentario',
        'calorias_totales'
    ];

    public function plato()
    {
        return $this->belongsTo(Plato::class, 'id_plato', 'id_plato');
    }
}
