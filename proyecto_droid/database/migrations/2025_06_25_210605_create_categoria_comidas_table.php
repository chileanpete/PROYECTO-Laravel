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
        Schema::create('categorias_comida', function (Blueprint $table) {
            $table->id('id_categoria');
            $table->string('nombre', 50);
            $table->text('descripcion')->nullable();
            $table->string('icono', 100)->nullable();
            $table->string('color_hex', 7)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('categorias_comida');
    }
};
