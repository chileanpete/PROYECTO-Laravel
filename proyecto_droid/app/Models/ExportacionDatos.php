<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportacionDatos extends Model
{
    protected $table = 'exportacion_datos';
    protected $primaryKey = 'id_exportacion';
    
    protected $fillable = [
        'id_usuario',
        'tipo_exportacion',
        'fecha_exportacion',
        'fecha_inicio_datos',
        'fecha_fin_datos',
        'archivo_generado',
        'compartido'
    ];

    protected $casts = [
        'fecha_exportacion' => 'datetime',
        'fecha_inicio_datos' => 'date',
        'fecha_fin_datos' => 'date',
        'compartido' => 'boolean'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
