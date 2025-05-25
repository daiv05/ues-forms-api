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
        Schema::create('menu_rutas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_padre')->nullable();
            $table->string('ruta',50)->nullable();
            $table->string('nombre',50);
            $table->string('icono',50)->nullable();
            $table->boolean('es_agrupador')->default(false);
            $table->boolean('requiere_auth')->default(true);
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_padre')->references('id')->on('menu_rutas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_rutas');
    }
};
