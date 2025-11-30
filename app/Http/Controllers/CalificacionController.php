<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Unidad;
use App\Models\Calificacion;
use App\Models\Alumno;

class CalificacionController extends Controller
{
    // =========================================================================
    // MÉTODOS PARA EL SELECTOR ORIGINAL (Paso 1: Grupo -> Materia -> Unidad)
    // =========================================================================

    /**
     * Muestra la página del selector inicial.
     */
    public function showSelector()
    {
        $grupos = Grupo::with('carrera')
                    ->where('esta_activo', 1) 
                    ->orderBy('nombre')
                    ->get();
                    
        return view('calificaciones.selector', [
            'grupos' => $grupos
        ]);
    }

    /**
     * API: Devuelve las materias de un grupo específico.
     */
    public function getMateriasPorGrupo(Grupo $grupo)
    {
        $materias = $grupo->materias()->orderBy('nombre')->get();
        return response()->json($materias);
    }

    /**
     * API: Devuelve las unidades de una materia específica.
     */
    public function getUnidadesPorMateria(Materia $materia)
    {
        $unidades = $materia->unidades()->orderBy('nombre')->get(); 
        return response()->json($unidades);
    }

    // =========================================================================
    // NUEVO MÉTODO: FLUJO DESDE MATERIAS (Paso 1: Materia -> Paso 2: Grupo)
    // =========================================================================

    /**
     * Muestra la pantalla para elegir Grupo, dado que ya eligieron la Materia.
     * Este es el método que usa el botón "Calificar" en materias.index
     */
    public function showSelectorPorMateria(Materia $materia)
    {
        // Buscamos los grupos que tienen esta materia asignada
        // Asegúrate de tener la relación 'grupos()' en tu modelo Materia
        $grupos = $materia->grupos()
                          ->where('esta_activo', 1)
                          ->orderBy('nombre')
                          ->get();

        return view('calificaciones.selector', [
            'materia' => $materia,
            'grupos' => $grupos
        ]);
    }


    // =========================================================================
    // MÉTODOS PARA LA "HOJA DE CALIFICACIÓN"
    // =========================================================================

    /**
     * Muestra la hoja de calificación final (la tabla).
     */
    public function showHojaDeCalificacion(Grupo $grupo, Materia $materia, Unidad $unidad)
    {
        // 1. Cargar los alumnos del grupo
        $alumnos = $grupo->alumnos()->orderBy('apellido_paterno')->get();

        // 2. Cargar los instrumentos de la unidad
        $instrumentos = $unidad->instrumentos()->orderBy('id')->get(); 

        // 3. Cargar las calificaciones QUE YA EXISTEN
        $calificacionesExistentes = Calificacion::whereIn('instrumento_id', $instrumentos->pluck('id'))
                                             ->whereIn('alumno_id', $alumnos->pluck('id'))
                                             ->get()
                                             ->keyBy(function ($item) {
                                                 return $item->alumno_id . '-' . $item->instrumento_id;
                                             });

        // 4. Mandar todo a la vista
        return view('calificaciones.hoja', [
            'grupo' => $grupo,
            'materia' => $materia,
            'unidad' => $unidad,
            'alumnos' => $alumnos,
            'instrumentos' => $instrumentos,
            'calificaciones' => $calificacionesExistentes
        ]);
    }

    /**
     * Guarda, actualiza O ELIMINA una calificación específica (vía Fetch/JS).
     */
    public function storeOrUpdate(Request $request)
    {
        // 1. VALIDACIÓN
        $datosValidados = $request->validate([
            'alumno_id' => 'required|integer|exists:alumnos,id',
            'instrumento_id' => 'required|integer|exists:instrumentos,id',
            'calificacion' => 'nullable|numeric|min:0|max:10' 
        ]);

        $alumnoId = $datosValidados['alumno_id'];
        $instrumentoId = $datosValidados['instrumento_id'];
        $calificacionValor = $datosValidados['calificacion'];

        try {
            // 2. CASO ELIMINAR: Si el valor es NULL (casilla vacía)
            if (is_null($calificacionValor)) {
                Calificacion::where('alumno_id', $alumnoId)
                            ->where('instrumento_id', $instrumentoId)
                            ->delete();

                return response()->json([
                    'success' => true, 
                    'message' => 'Calificación eliminada correctamente.'
                ]);
            }

            // 3. CASO GUARDAR/ACTUALIZAR: Si hay un número (incluido el 0)
            Calificacion::updateOrCreate(
                [
                    'alumno_id' => $alumnoId,
                    'instrumento_id' => $instrumentoId
                ],
                [
                    'calificacion_obtenida' => $calificacionValor
                ]
            );

            return response()->json([
                'success' => true, 
                'message' => 'Calificación guardada.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
}