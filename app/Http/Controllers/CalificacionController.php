<?php

namespace App\Http\Controllers;


use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Alumno;
use App\Models\CriterioEvaluacion;
use App\Models\Calificacion;
use Illuminate\Http\Request;

class CalificacionController extends Controller
{
    /**
     * Prepara y muestra la pantalla de mando de calificación.
     */
    public function create(Grupo $grupo, Materia $materia)
    {
        $alumnos = $grupo->alumnos; 
        $criterios = CriterioEvaluacion::where('materia_id', $materia->id)
                                        ->with('subtareas') 
                                        ->get();

        $calificacionesExistentes = Calificacion::where('grupo_id', $grupo->id)
                                                ->whereIn('criterio_evaluacion_id', $criterios->pluck('id'))
                                                ->get();
        
        $calificacionesOrganizadas = [];
        foreach ($calificacionesExistentes as $calificacion) {
            $alumnoId = $calificacion->alumno_id;
            $criterioId = $calificacion->criterio_evaluacion_id;
            $subtareaId = $calificacion->subtarea_id ?? 'main'; 
            
            $calificacionesOrganizadas[$alumnoId][$criterioId][$subtareaId] = $calificacion->puntuacion_decimal;
        }

        
        //  cálculo de los promedios finales
       
        $promediosFinales = [];
        foreach ($alumnos as $alumno) {
            $promediosFinales[$alumno->id] = $alumno->calcularCalificacionFinal($materia->id, $grupo->id);
        }

        return view('calificaciones.create', compact(
            'alumnos', 
            'grupo', 
            'materia', 
            'criterios', 
            'calificacionesOrganizadas',
            'promediosFinales' 
        ));
    }

    /**
     * Almacena las calificaciones enviadas.ç
     */
    public function store(Request $request)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'materia_id' => 'required|exists:materias,id',
            'calificaciones' => 'required|array',
        ]);
        
        foreach ($request->calificaciones as $alumno_id => $criterios) {
            foreach ($criterios as $criterio_id => $data) {
                if (isset($data['puntuacion'])) {
                    Calificacion::updateOrCreate(
                        [
                            'alumno_id' => $alumno_id,
                            'grupo_id' => $request->grupo_id,
                            'criterio_evaluacion_id' => $criterio_id,
                            'subtarea_id' => null,
                        ],
                        ['puntuacion_decimal' => $data['puntuacion'] ?? null]
                    );
                } else {
                    foreach($data as $subtarea_id => $subtarea_data) {
                         Calificacion::updateOrCreate(
                            [
                                'alumno_id' => $alumno_id,
                                'grupo_id' => $request->grupo_id,
                                'criterio_evaluacion_id' => $criterio_id,
                                'subtarea_id' => $subtarea_id,
                            ],
                            ['puntuacion_decimal' => $subtarea_data['puntuacion'] ?? null]
                        );
                    }
                }
            }
        }

        return back()->with('success', 'Calificaciones guardadas y promedios actualizados exitosamente.');
    }
}