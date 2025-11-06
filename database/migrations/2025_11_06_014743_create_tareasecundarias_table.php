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
    Schema::create('tareasecundarias', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->unsignedInteger('porcentaje'); // El peso de esta tarea secundaria

        // Llave foránea que la conecta al instrumento padre
        $table->foreignId('instrumento_id')
              ->constrained('instrumentos') // Se conecta a la tabla 'instrumentos'
              ->onDelete('cascade'); // Si se borra el instrumento, se borran sus tareas

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareasecundarias');
    }
};
