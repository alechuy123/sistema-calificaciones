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
    Schema::create('instrumentos', function (Blueprint $table) {
        $table->id();
        
        // Llave foránea que lo conecta a la tabla 'unidads'
        $table->foreignId('unidad_id')
              ->constrained('unidads') // Se enlaza a la tabla que creamos antes
              ->onDelete('cascade'); // Si se borra la unidad, se borran sus instrumentos

        $table->string('nombre');
        $table->integer('porcentaje'); // 1-100
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instrumentos');
    }
};
