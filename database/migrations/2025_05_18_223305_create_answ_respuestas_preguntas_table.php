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
        Schema::create('answ_respuestas_preguntas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pregunta');
            $table->unsignedBigInteger('id_encuesta_respuesta');
            $table->string('txt',255);
            $table->boolean('valor');
            $table->boolean('es_abierta');
            $table->integer('num_escala');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('id_pregunta')->references('id')->on('srvy_preguntas');
            $table->foreign('id_encuesta_respuesta')->references('id')->on('answ_encuesta_respuestas');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answ_respuestas_preguntas');
    }
};
