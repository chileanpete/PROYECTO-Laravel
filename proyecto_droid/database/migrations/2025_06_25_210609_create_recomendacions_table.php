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
        Schema::create('recomendaciones', function (Blueprint $table) {
            $table->id('id_recomendacion');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->string('tipo_recomendacion', 50);
            $table->string('titulo', 200);
            $table->text('descripcion');
            $table->foreignId('id_plato')->nullable()->constrained('platos', 'id_plato')->onDelete('set null');
            $table->foreignId('id_rutina_ejercicio')->nullable()->constrained('rutinas_ejercicio', 'id_rutina')->onDelete('set null');
            $table->integer('prioridad')->default(1);
            $table->timestamp('fecha_generacion');
            $table->boolean('visto')->default(false);
            $table->boolean('aplicado')->default(false);
            $table->date('fecha_expiracion');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recomendaciones');
    }
};
