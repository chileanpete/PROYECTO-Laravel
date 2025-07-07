<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plato extends Model
{
    protected $primaryKey = 'id_plato';
    //

    public function lugar()
    {
        return $this->belongsTo(LugarComida::class, 'id_lugar', 'id_lugar');
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaComida::class, 'id_categoria', 'id_categoria');
    }
}
