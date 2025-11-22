<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            // Cambiamos la columna para que acepte nulos (nullable)
            $table->string('matricula')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            // Revertimos el cambio (volver a no nulo)
            $table->string('matricula')->nullable(false)->change();
        });
    }
};
