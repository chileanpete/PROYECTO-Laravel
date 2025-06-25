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
        Schema::create('favoritos_platos', function (Blueprint $table) {
            $table->id('id_favorito');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->foreignId('id_plato')->constrained('platos', 'id_plato')->onDelete('cascade');
            $table->timestamp('fecha_agregado');
            $table->timestamps();
            
            $table->unique(['id_usuario', 'id_plato']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('favoritos_platos');
    }
};
