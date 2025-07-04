<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ObjetivoAlimentacion;


class Usuario extends Model
{
    // Define explícitamente la tabla
    protected $table = 'usuarios';

    // Define la clave primaria personalizada
    protected $primaryKey = 'id_usuario';

    // Define si la clave primaria es auto incrementable (en tu caso sí)
    public $incrementing = true;

    // Tipo de la clave primaria (entero)
    protected $keyType = 'int';

    // Si usas timestamps en la tabla
    public $timestamps = true;

    public function objetivosAlimentacion()
    {
        return $this->hasMany(ObjetivoAlimentacion::class, 'id_usuario', 'id_usuario');
    }
}
