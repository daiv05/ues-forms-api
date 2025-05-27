<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('answ_opciones_seleccionadas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pregunta_opcion');
            $table->unsignedBigInteger('id_respuesta_pregunta');
            $table->integer('orden_final');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('id_pregunta_opcion')->references('id')->on('srvy_preguntas_opciones');
            $table->foreign('id_respuesta_pregunta')->references('id')->on('answ_respuestas_preguntas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answ_opciones_seleccionadas');
    }
};
