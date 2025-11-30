<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
// --- ¡IMPORTACIONES AÑADIDAS! ---
// Tu editor marcaba error porque faltaban estas líneas
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Models\Grupo;
use App\Models\Calificacion;
use App\Models\CriterioEvaluacion;
// --- FIN DE IMPORTACIONES ---

class Alumno extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'matricula',
        'carrera_id',
        'ciclo_escolar_id',
        'esta_activo'
    ];

    protected $casts = [
        'esta_activo' => 'boolean',
    ];

    // --- Relaciones de Uno a Muchos (belongsTo) ---

    /**
     * Un Alumno pertenece a una Carrera.
     */
    public function carrera()
    {
        // Esto ya no marcará error
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    /**
     * Un Alumno pertenece a un Ciclo Escolar.
     */
    public function cicloEscolar()
    {
        // Esto ya no marcará error
        return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id');
    }

    // --- Relación de Muchos a Muchos (belongsToMany) ---

    /**
     * Un Alumno está matriculado en varios Grupos (para calificar).
     */
    public function grupos()
    {
        // Esto ya no marcará error
        return $this->belongsToMany(Grupo::class, 'alumno_grupo', 'alumno_id', 'grupo_id');
    }


    // --- CÓDIGO AGREGADO PARA EL MÓDULO DE CALIFICACIONES ---

    /**
     * Relación de uno a muchos con Calificaciones.
     */
    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'alumno_id');
    }

    /**
     * Lógica para calcular la calificación final de un alumno.
     */
    public function calcularCalificacionFinal(int $materia_id, int $grupo_id)
    {
        $criteriosDeLaMateria = CriterioEvaluacion::where('materia_id', $materia_id)
                                                ->where('grupo_id', $grupo_id)
                                                ->with('subtareas')
                                                ->get();

        $calificacionFinal = 0;

        foreach ($criteriosDeLaMateria as $criterio) {

            $calificaciones = $this->calificaciones()
                                     ->where('grupo_id', $grupo_id)
                                     ->where('criterio_evaluacion_id', $criterio->id)
                                     ->get();

            if ($calificaciones->isEmpty()) {
                continue;
            }

            $sumaPonderadaDelCriterio = 0;

            if ($criterio->subtareas->count() > 0) {
                $sumaSubtareas = $calificaciones->sum('puntuacion_decimal');
                $promedioSubtareas = $sumaSubtareas / $criterio->subtareas->count();
                $sumaPonderadaDelCriterio = ($promedioSubtareas * ($criterio->porcentaje_decimal / 100));

            } else {
                $puntuacion = $calificaciones->first()->puntuacion_decimal ?? 0;
                $sumaPonderadaDelCriterio = ($puntuacion * ($criterio->porcentaje_decimal / 100));
            }

            $calificacionFinal += $sumaPonderadaDelCriterio;
        }

        return round($calificacionFinal, 2);
    }

    // ==========================================================
    // --- FUNCIÓN PARA LA PRUEBA UNITARIA ---
    // ==========================================================

    /**
     * Define un accesor para obtener el nombre completo del alumno.
     * EJ: $alumno->nombre_completo
     */
    protected function nombreCompleto(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return trim($attributes['nombre'] . ' ' . $attributes['apellido_paterno'] . ' ' . $attributes['apellido_materno']);
            }
        );
    }
    public function activar()
    {
        $this->esta_activo = true;
        $this->save();
    }

    /**
     * Desactiva al alumno.
     */
    public function desactivar()
    {
        $this->esta_activo = false;
        $this->save();
    }
}
