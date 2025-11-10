<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// --- ¡NUEVO IMPORT! ---
// Necesario para comparar fechas en la nueva función
use Carbon\Carbon;

class Cuatrimestre extends Model
{
    // --- ¡AÑADIDO! ---
    // Es una buena práctica tener el Factory
    use HasFactory;

    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin', 'esta_activo'];

    // ==========================================================
    // --- ¡NUEVA FUNCIÓN PARA LA PRUEBA UNITARIA! ---
    // ==========================================================

    /**
     * (Lógica para Prueba Unitaria)
     * Verifica si la fecha actual está dentro del rango del cuatrimestre.
     *
     * @return boolean
     */
    public function estaVigenteHoy(): bool
    {
        $hoy = Carbon::now();
        $inicio = Carbon::parse($this->fecha_inicio);
        $fin = Carbon::parse($this->fecha_fin);

        // Comprueba si "hoy" está entre la fecha de inicio y fin
        return $hoy->between($inicio, $fin);
    }
}
