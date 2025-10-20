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
        // CORRECCIÓN: Se cambió 'calificaciones' a 'calificacions'
        Schema::create('calificacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade');
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->foreignId('criterio_evaluacion_id')->constrained('criterio_evaluacions')->onDelete('cascade');
            
            $table->foreignId('subtarea_id')->nullable()->constrained('subtareas')->onDelete('cascade');
            
            $table->decimal('puntuacion_decimal', 5, 2)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // CORRECCIÓN: Se cambió 'calificaciones' a 'calificacions'
        Schema::dropIfExists('calificacions');
    }
};