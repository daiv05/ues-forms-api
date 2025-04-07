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
        Schema::create('srvy_encuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('users');
            $table->foreignId('id_grupo_meta')->constrained('srvy_grupos_metas');
            $table->string('codigo', 50);
            $table->string('titulo', 50);
            $table->string('objetivo', 50);
            $table->string('instrucciones', 255);
            $table->date('fecha_publicacion');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('srvy_encuestas');
    }
};
