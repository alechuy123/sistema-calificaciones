<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// --- ¡NUEVAS IMPORTACIONES! ---
// (Para corregir los errores de "clase no encontrada" en tu editor)
use App\Models\Materia;
use App\Models\Cuatrimestre;
use App\Models\Carrera;
use App\Models\Alumno;

class Grupo extends Model
{
    use HasFactory;

    // --- CAMBIO 1 ---
    // Se elimina 'materia_id' (ya no existe en la tabla)
    // Se añade 'grupo_anterior_id' (para el historial que pidió la maestra)
    protected $fillable = [
        'nombre',
        // 'materia_id', // <- ELIMINADO
        'cuatrimestre_id',
        'carrera_id',
        'esta_activo',
        'grupo_anterior_id' // <- AÑADIDO (para el historial)
    ];

    /**
     * CLAVE: Asegura que el valor de la base de datos sea tratado como booleano
     * (Esto se queda igual)
     */
    protected $casts = [
        'esta_activo' => 'boolean',
    ];

    /**
     * --- CAMBIO 2 ---
     * Un grupo ahora tiene MUCHAS materias (a través de la tabla pivote 'grupo_materia').
     * La función 'materia()' (singular) ha sido eliminada.
     */
    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'grupo_materia');
    }

    /**
     * Un grupo se imparte en un cuatrimestre específico.
     * (Esto se queda igual)
     */
    public function cuatrimestre()
    {
        return $this->belongsTo(Cuatrimestre::class, 'cuatrimestre_id');
    }

    /**
     * Un grupo pertenece a una Carrera.
     * (Esto se queda igual)
     */
    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    /**
     * Un grupo tiene muchos alumnos (a través de la tabla pivote 'alumno_grupo').
     * (Esto se queda igual)
     */
    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'alumno_grupo', 'grupo_id', 'alumno_id');
    }

    /**
     * --- CAMBIO 3 (NUEVO) ---
     * Relaciones para el historial (promover grupo)
     */

    /**
     * Obtiene el grupo que fue el cuatrimestre pasado (el "padre" de este grupo).
     */
    public function grupoAnterior()
    {
        // Un grupo pertenece a su versión anterior
        return $this->belongsTo(Grupo::class, 'grupo_anterior_id');
    }

    /**
     * Obtiene el grupo que será el próximo cuatrimestre (el "hijo" de este grupo).
     */
    public function grupoSiguiente()
    {
        // Un grupo tiene una (o ninguna) versión futura
        return $this->hasOne(Grupo::class, 'grupo_anterior_id');
    }

    // ==========================================================
    // --- ¡NUEVA FUNCIÓN PARA LA PRUEBA UNITARIA! ---
    // ==========================================================

    /**
     * (Lógica para Prueba Unitaria)
     * Intenta sugerir un nombre para el siguiente cuatrimestre.
     * Ej: "G-ISC-2A" se convierte en "G-ISC-3A"
     */
    public function getNombreSugeridoPromocion(): string
    {
        // Busca un número al final del nombre
        $count = 0;
        $sugerencia = preg_replace_callback('/(\d+)$/', function ($matches) {
            // Si encuentra un número, lo incrementa
            return $matches[1] + 1;
        }, $this->nombre, 1, $count);

        // Si no encontró un número, solo añade "-PROMO"
        if ($count === 0) {
            return $this->nombre . '-PROMO';
        }

        return $sugerencia;
    }
}
