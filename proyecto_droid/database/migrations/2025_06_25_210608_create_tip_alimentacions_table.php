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
        Schema::create('tip_alimentacion', function (Blueprint $table) {
            $table->id('id_tip');
            $table->string('titulo', 150);
            $table->text('contenido');
            $table->string('categoria', 50);
            $table->integer('nivel_importancia')->default(1);
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_creacion');
            $table->string('imagen_url', 500)->nullable();
            $table->string('fuente', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tips_alimentacion');
    }
};
