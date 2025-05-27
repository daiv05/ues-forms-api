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
        Schema::create('answ_encuestados', function (Blueprint $table) {
            $table->id();
            $table->string('correo',50);
            $table->string('nombres',50);
            $table->string('apellidos',50);
            $table->date('fecha_nacimiento');
            $table->string('telefono',20);
            $table->integer('edad');
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answ_encuestados');
    }
};
