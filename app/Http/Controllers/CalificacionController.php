<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Unidad;
use App\Models\Calificacion;
use App\Models\Alumno; // Asegúrate de que este modelo exista y esté importado

class CalificacionController extends Controller
{
    // =========================================================================
    // MÉTODOS PARA TU NUEVA PÁGINA "SELECTOR" (Paso 1B)
    // =========================================================================

    /**
     * Muestra la página del selector de 3 pasos (Grupo -> Materia -> Unidad).
     */
    public function showSelector()
    {
        // Cargamos solo grupos activos y con sus relaciones de carrera
        // Asumiendo que tienes un campo 'esta_activo'
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
     * Usado por el JavaScript del selector.
     */
    public function getMateriasPorGrupo(Grupo $grupo)
    {
        // Carga las materias que están asignadas a este grupo
        // (Asegúrate de tener la relación 'materias' en tu modelo Grupo)
        $materias = $grupo->materias()->orderBy('nombre')->get();
        return response()->json($materias);
    }

    /**
     * API: Devuelve las unidades de una materia específica.
     * Usado por el JavaScript del selector.
     */
    public function getUnidadesPorMateria(Materia $materia)
    {
        // Carga las unidades que pertenecen a esta materia
        // (Asegúrate de tener la relación 'unidades' en tu modelo Materia)
        $unidades = $materia->unidades()->orderBy('nombre')->get(); 
        return response()->json($unidades);
    }


    // =========================================================================
    // MÉTODOS PARA LA "HOJA DE CALIFICACIÓN" (Los que ya habíamos hecho)
    // =========================================================================

    /**
     * Muestra la hoja de calificación final (la tabla).
     */
    public function showHojaDeCalificacion(Grupo $grupo, Materia $materia, Unidad $unidad)
    {
        // 1. Cargar los alumnos del grupo
        // (Asegúrate de tener la relación 'alumnos' en tu modelo Grupo)
        $alumnos = $grupo->alumnos()->orderBy('apellido_paterno')->get();

        // 2. Cargar los instrumentos de la unidad
        // (Asegúrate de tener la relación 'instrumentos' en tu modelo Unidad)
        $instrumentos = $unidad->instrumentos()->orderBy('id')->get(); 

        // 3. Cargar las calificaciones QUE YA EXISTEN
        $calificacionesExistentes = Calificacion::whereIn('instrumento_id', $instrumentos->pluck('id'))
                                             ->whereIn('alumno_id', $alumnos->pluck('id'))
                                             ->get()
                                             ->keyBy(function ($item) {
                                                 // Creamos una llave "alumno_id-instrumento_id"
                                                 return $item->alumno_id . '-' . $item->instrumento_id;
                                             });

        // 4. Mandar todo a la vista
        return view('calificaciones.hoja', [
            'grupo' => $grupo,
            'materia' => $materia, // Pasamos la materia a la vista
            'unidad' => $unidad,
            'alumnos' => $alumnos,
            'instrumentos' => $instrumentos,
            'calificaciones' => $calificacionesExistentes
        ]);
    }

    /**
     * Guarda o actualiza una calificación específica (vía Fetch/JS).
     */
    public function storeOrUpdate(Request $request)
    {
        // Validar los datos que llegan
        $datosValidados = $request->validate([
            'alumno_id' => 'required|integer|exists:alumnos,id',
            'instrumento_id' => 'required|integer|exists:instrumentos,id',
            'calificacion' => 'required|numeric|min:0|max:10' // O la escala que uses
        ]);

        try {
            $calificacion = Calificacion::updateOrCreate(
                [
                    // Qué buscar
                    'alumno_id' => $datosValidados['alumno_id'],
                    'instrumento_id' => $datosValidados['instrumento_id']
                ],
                [
                    // Con qué actualizar/crear
                    'calificacion_obtenida' => $datosValidados['calificacion']
                ]
            );

            // Responder con éxito
            return response()->json([
                'success' => true, 
                'message' => 'Calificación guardada.'
            ]);

        } catch (\Exception $e) {
            // Responder con error
            return response()->json([
                'success' => false, 
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
}