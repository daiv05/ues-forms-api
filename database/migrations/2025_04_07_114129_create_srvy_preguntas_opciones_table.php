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
        Schema::create('srvy_preguntas_opciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pregunta')->constrained('srvy_preguntas')->cascadeOnDelete();
            $table->string('opcion', 50);
            $table->integer('orden_inicial')->default(0);
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('srvy_preguntas_opciones');
    }
};
