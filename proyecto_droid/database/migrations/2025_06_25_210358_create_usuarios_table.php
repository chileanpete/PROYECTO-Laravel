<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('email', 100)->unique();
            $table->string('password_hash', 255);
            $table->string('nombre', 50);
            $table->string('apellidos', 100);
            $table->date('fecha_nacimiento');
            $table->char('genero', 1);
            $table->integer('altura_cm');
            $table->decimal('peso_kg', 5, 2);
            $table->string('nivel_actividad', 20);
            $table->string('objetivo_principal', 30);
            $table->timestamp('fecha_registro');
            $table->timestamp('fecha_ultimo_acceso')->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('puntos_totales')->default(0);
            $table->text('preferencias_alimentarias')->nullable();
            $table->text('alergias')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};
