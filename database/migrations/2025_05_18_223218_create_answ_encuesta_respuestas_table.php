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
        Schema::create('answ_encuesta_respuestas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_encuesta');
            $table->unsignedBigInteger('id_encuestado');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('id_encuesta')->references('id')->on('srvy_encuestas');
            $table->foreign('id_encuestado')->references('id')->on('answ_encuestados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answ_encuesta_respuestas');
    }
};
