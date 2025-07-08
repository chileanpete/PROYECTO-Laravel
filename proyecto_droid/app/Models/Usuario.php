<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ObjetivoAlimentacion;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    // Define si la clave primaria es auto incrementable 
    public $incrementing = true;

    // Tipo de la clave primaria (entero)
    protected $keyType = 'int';

    // Si usas timestamps en la tabla
    public $timestamps = true;
    
    protected $fillable = [
        'email',
        'password_hash',
        'nombre',
        'apellidos',
        'fecha_nacimiento',
        'genero',
        'altura_cm',
        'peso_kg',
        'nivel_actividad',
        'objetivo_principal',
        'fecha_registro',
        'fecha_ultimo_acceso',
        'activo',
        'puntos_totales',
        'preferencias_alimentarias',
        'alergias'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_registro' => 'datetime',
        'fecha_ultimo_acceso' => 'datetime',
        'activo' => 'boolean',
        'peso_kg' => 'decimal:2',
        'altura_cm' => 'integer',
        'puntos_totales' => 'integer'
    ];

    // Relaciones
    public function configuracion(): HasOne
    {
        return $this->hasOne(ConfiguracionUsuario::class, 'id_usuario', 'id_usuario');
    }

    public function historialPeso(): HasMany
    {
        return $this->hasMany(HistorialPesoImc::class, 'id_usuario', 'id_usuario');
    }

    public function registrosActividad(): HasMany
    {
        return $this->hasMany(RegistroActividadFisica::class, 'id_usuario', 'id_usuario');
    }

    public function registrosConsumo(): HasMany
    {
        return $this->hasMany(RegistroConsumo::class, 'id_usuario', 'id_usuario');
    }

    public function favoritosPlatos(): HasMany
    {
        return $this->hasMany(FavoritoPlato::class, 'id_usuario', 'id_usuario');
    }

    public function menusDiarios(): HasMany
    {
        return $this->hasMany(MenuDiario::class, 'id_usuario', 'id_usuario');
    }

    public function objetivosAlimentacion(): HasMany
    {
        return $this->hasMany(ObjetivoAlimentacion::class, 'id_usuario', 'id_usuario');
    }

    public function usuarioDesafios(): HasMany
    {
        return $this->hasMany(UsuarioDesafio::class, 'id_usuario', 'id_usuario');
    }

    public function usuarioEventos(): HasMany
    {
        return $this->hasMany(UsuarioEvento::class, 'id_usuario', 'id_usuario');
    }

    public function inscripcionesTalleres(): HasMany
    {
        return $this->hasMany(InscripcionTaller::class, 'id_usuario', 'id_usuario');
    }

    public function recomendaciones(): HasMany
    {
        return $this->hasMany(Recomendacion::class, 'id_usuario', 'id_usuario');
    }

    public function exportacionesDatos(): HasMany
    {
        return $this->hasMany(ExportacionDatos::class, 'id_usuario', 'id_usuario');
    }
}
