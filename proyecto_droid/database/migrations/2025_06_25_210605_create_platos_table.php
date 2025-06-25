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
        Schema::create('platos', function (Blueprint $table) {
            $table->id('id_plato');
            $table->foreignId('id_lugar')->constrained('lugares_comida', 'id_lugar')->onDelete('cascade');
            $table->foreignId('id_categoria')->constrained('categorias_comida', 'id_categoria')->onDelete('cascade');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 8, 2);
            $table->integer('calorias_por_porcion');
            $table->decimal('proteinas_g', 5, 2)->default(0);
            $table->decimal('carbohidratos_g', 5, 2)->default(0);
            $table->decimal('grasas_g', 5, 2)->default(0);
            $table->decimal('fibra_g', 5, 2)->default(0);
            $table->decimal('azucares_g', 5, 2)->default(0);
            $table->decimal('sodio_mg', 7, 2)->default(0);
            $table->boolean('disponible')->default(true);
            $table->boolean('es_vegetariano')->default(false);
            $table->boolean('es_vegano')->default(false);
            $table->boolean('sin_gluten')->default(false);
            $table->string('imagen_url', 500)->nullable();
            $table->timestamp('fecha_creacion');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('platos');
    }
};
