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
        Schema::create('qst_categorias_preguntas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_clase_pregunta')->constrained('qst_clases_preguntas');
            $table->string('codigo', 50);
            $table->string('nombre', 50);
            $table->string('descripcion', 50);
            $table->integer('max_text_length')->default(0);
            $table->integer('max_seleccion_items')->default(0);
            $table->boolean('es_escala_numerica')->default(false);
            $table->boolean('es_booleano')->default(false);
            $table->boolean('permite_otros')->default(false);
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qst_categorias_preguntas');
    }
};
