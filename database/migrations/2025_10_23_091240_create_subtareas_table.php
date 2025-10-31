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
    Schema::create('subtareas', function (Blueprint $table) {
        $table->id();

        // Llave foránea que lo conecta a la tabla 'instrumentos'
        $table->foreignId('instrumento_id')
              ->constrained('instrumentos')
              ->onDelete('cascade');

        $table->string('nombre'); // Ej: "Portada", "Introducción", "Conclusión"
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subtareas');
    }
};
