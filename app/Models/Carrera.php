<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    use HasFactory;

    // CORRECCIÓN: Añadimos 'esta_activo' para que el controlador pueda cambiar su estado (desactivación lógica)
    protected $fillable = ['nombre', 'descripcion', 'esta_activo'];

    // --- Relaciones de Muchos a Muchos ---

    /**
     * Una Carrera tiene muchas Materias.
     * Esta relación usa la tabla pivote 'carrera_materia'.
     */
    public function materias()
    {
        // Se define la tabla pivote 'carrera_materia'
        return $this->belongsToMany(Materia::class, 'carrera_materia', 'carrera_id', 'materia_id');
    }

    // --- Relación con Alumnos (que ya tenías) ---

    /**
     * Una Carrera tiene muchos Alumnos.
     */
    public function alumnos()
    {
        // Se asume que el ID de la carrera está en la tabla alumnos
        return $this->hasMany(Alumno::class, 'carrera_id');
    }

    // ==========================================================
    // --- ¡NUEVA RELACIÓN AÑADIDA PARA CORREGIR EL ERROR! ---
    // ==========================================================

    /**
     * Una Carrera tiene muchos Grupos.
     * Esta es la función que faltaba y causaba el error 500 en el AJAX.
     */
    public function grupos()
    {
        // Un Grupo pertenece a una Carrera (la llave foránea 'carrera_id' está en la tabla 'grupos')
        return $this->hasMany(Grupo::class, 'carrera_id');
    }
}
