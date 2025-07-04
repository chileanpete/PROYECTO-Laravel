<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desafio extends Model
{
    protected $casts = [
        'objetivos_relacionados' => 'array',
    ];
}
