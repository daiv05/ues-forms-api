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
        Schema::create('qst_clases_preguntas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tipo_pregunta')->constrained('qst_tipos_preguntas');
            $table->string('nombre', 50);
            $table->boolean('requiere_lista')->default(false);
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qst_clases_preguntas');
    }
};
