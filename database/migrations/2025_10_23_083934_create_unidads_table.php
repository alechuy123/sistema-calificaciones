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
    Schema::create('unidads', function (Blueprint $table) {
        $table->id();
        
        // Esta es la llave foránea que la conecta con 'materias'
        $table->foreignId('materia_id')
              ->constrained('materias') // Se enlaza a la tabla materias
              ->onDelete('cascade'); // Si se borra la materia, se borran sus unidades

        $table->string('nombre');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidads');
    }
};
