<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoritoPlato extends Model
{
    protected $table = 'favoritos_platos';
    protected $primaryKey = 'id_favorito';
    
    protected $fillable = [
        'id_usuario',
        'id_plato',
        'fecha_agregado'
    ];

    protected $casts = [
        'fecha_agregado' => 'datetime'
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
}
