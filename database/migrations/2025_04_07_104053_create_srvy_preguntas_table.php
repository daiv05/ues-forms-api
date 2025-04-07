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
        Schema::create('srvy_preguntas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_catalogo_tipo_pregunta')->constrained('qst_catalogo_tipos_preguntas');
            $table->foreignId('id_encuesta')->constrained('srvy_encuestas');
            $table->string('descripcion', 50);
            $table->boolean('es_abierta')->default(false);
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('srvy_preguntas');
    }
};
