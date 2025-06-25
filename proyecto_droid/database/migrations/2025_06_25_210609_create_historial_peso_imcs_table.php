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
        Schema::create('historial_peso_imc', function (Blueprint $table) {
            $table->id('id_historial');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->decimal('peso_kg', 5, 2);
            $table->integer('altura_cm');
            $table->decimal('imc_calculado', 4, 2);
            $table->string('categoria_imc', 30);
            $table->timestamp('fecha_registro');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('historial_peso_imc');
    }
};
