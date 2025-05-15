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
            $table->foreignId('id_grupo_meta')->nullable()->constrained('srvy_grupos_metas');
            $table->foreignId('id_estado')->constrained('ctl_estados');
            $table->string('codigo', 50)->unique();
            $table->string('titulo', 50);
            $table->text('objetivo');
            $table->text('descripcion');
            $table->date('fecha_publicacion')->nullable();
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
