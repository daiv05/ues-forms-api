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
        Schema::create('ctrl_solicitudes_registros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('id_estado')
                ->constrained('ctl_estados')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('id_usuario_autoriza')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->text('justificacion_solicitud')->nullable();
            $table->text('justificacion_rechazo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ctrl_solicitudes_registros');
    }
};
