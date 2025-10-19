<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// AGREGADO: Es importante importar los modelos que se usarán en los cálculos.
use App\Models\CriterioEvaluacion; 

class Alumno extends Model
{
    use HasFactory;

    // FUSIONADO: Contiene los campos de ambos proyectos.
    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'matricula',
        'carrera_id',
        'ciclo_escolar_id',
        'esta_activo' // NUEVO: Para la desactivación lógica
    ];

    // MANTENIDO: Asegurar que el campo sea booleano
    protected $casts = [
        'esta_activo' => 'boolean',
    ];

    // --- Relaciones de Uno a Muchos (belongsTo) ---

    /**
     * Un Alumno pertenece a una Carrera.
     */
    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    /**
     * Un Alumno pertenece a un Ciclo Escolar.
     */
    public function cicloEscolar()
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id');
    }

    // --- Relación de Muchos a Muchos (belongsToMany) ---

    /**
     * Un Alumno está matriculado en varios Grupos (para calificar).
     */
    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'alumno_grupo', 'alumno_id', 'grupo_id');
    }


    // --- CÓDIGO AGREGADO PARA EL MÓDULO DE CALIFICACIONES ---

    /**
     * Relación de uno a muchos con Calificaciones.
     * Un alumno puede tener muchas calificaciones registradas.
     */
    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'alumno_id');
    }

    /**
     * Lógica para calcular la calificación final de un alumno.
     * Calcula la calificación ponderada para una materia y un grupo específicos
     */
    public function calcularCalificacionFinal(int $materia_id, int $grupo_id)
    {
        //Obtenemos todos los criterios de la materia con sus subtareas.
        $criteriosDeLaMateria = CriterioEvaluacion::where('materia_id', $materia_id)
                                                ->where('grupo_id', $grupo_id) // Asegurarse que sean del grupo correcto
                                                ->with('subtareas')
                                                ->get();

        $calificacionFinal = 0;

        //Iteramos sobre cada criterio principal (Ejercicios, Prácticas, Examen).
        foreach ($criteriosDeLaMateria as $criterio) {
            
            // Obtenemos las calificaciones de este alumno para este criterio específico.
            $calificaciones = $this->calificaciones()
                                     ->where('grupo_id', $grupo_id)
                                     ->where('criterio_evaluacion_id', $criterio->id)
                                     ->get();

            if ($calificaciones->isEmpty()) {
                continue; // Si no hay calificaciones para este criterio, lo saltamos.
            }

            $sumaPonderadaDelCriterio = 0;

            //Verificamos si el criterio tiene subtareas.
            if ($criterio->subtareas->count() > 0) {
                //Criterio con subtareas
                $sumaSubtareas = $calificaciones->sum('puntuacion_decimal');
                $promedioSubtareas = $sumaSubtareas / $criterio->subtareas->count();

                // Calculamos el valor ponderado 
                $sumaPonderadaDelCriterio = ($promedioSubtareas * ($criterio->porcentaje_decimal / 100));

            } else {
                //Criterio sin subtareas
                $puntuacion = $calificaciones->first()->puntuacion_decimal ?? 0;

                // Calculamos el valor ponderado 
                $sumaPonderadaDelCriterio = ($puntuacion * ($criterio->porcentaje_decimal / 100));
            }
            
            //Sumamos el resultado de este criterio a la calificación final.
            $calificacionFinal += $sumaPonderadaDelCriterio;
        }

        // Devolvemos la calificación final redondeada a 2 decimales.
        return round($calificacionFinal, 2);
    }

    
}