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
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();

            // Llaves foráneas
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade');
            $table->foreignId('instrumento_id')->constrained('instrumentos')->onDelete('cascade');
            
            // La nota
            $table->decimal('calificacion_obtenida', 5, 2); // Ej. 10.00 o 9.50

            $table->timestamps();

            // Llave única para evitar duplicados
            // (Un alumno solo puede tener UNA nota por instrumento)
            $table->unique(['alumno_id', 'instrumento_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};