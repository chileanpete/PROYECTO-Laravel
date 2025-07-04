<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjetivoAlimentacion extends Model
{
    protected $table = 'objetivos_alimentacion';  // nombre exacto de la tabla

    protected $primaryKey = 'id_objetivo'; // clave primaria según tu migration

    public $timestamps = true;

    // Si quieres, puedes agregar relaciones aquí

}
