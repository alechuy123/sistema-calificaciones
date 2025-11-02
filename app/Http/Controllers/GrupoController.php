<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Cuatrimestre;
use App\Models\Alumno;
use App\Models\Carrera; // Asegúrate de que este import exista

class GrupoController extends Controller
{
    /**
     * Muestra la lista de todos los grupos.
     */
    public function index()
    {
        // --- ¡CAMBIO AQUÍ! ---
        // Se añade el "where('esta_activo', 1)" para que el listado
        // principal solo muestre los grupos que están activos.
        $grupos = Grupo::with(['materias', 'cuatrimestre', 'carrera'])
                        ->where('esta_activo', 1)
                        ->get();
        // --- FIN DEL CAMBIO ---

        return view('grupos.index', compact('grupos'));
    }

    /**
     * Muestra el formulario para crear un nuevo grupo.
     */
    public function create()
    {
        // Esto está correcto, solo pasamos carreras y ciclos.
        // Los grupos se cargan con JS.
        $cuatrimestres = Cuatrimestre::where('esta_activo', 1)->get();
        $carreras = Carrera::where('esta_activo', 1)->get();

        return view('grupos.create', compact('cuatrimestres', 'carreras'));
    }

    /**
     * Guarda un nuevo grupo en la base de datos.
     */
    public function store(Request $request)
    {
        // (Esto ya estaba actualizado para 'materias[]' (array), está correcto)
        $request->validate([
            'nombre' => 'required|max:255|unique:grupos',
            'materias' => 'required|array',
            'materias.*' => 'exists:materias,id',
            'cuatrimestre_id' => 'required|exists:cuatrimestres,id',
            'carrera_id' => 'required|exists:carreras,id',
        ]);

        $grupo = Grupo::create([
            'nombre' => $request->nombre,
            'cuatrimestre_id' => $request->cuatrimestre_id,
            'carrera_id' => $request->carrera_id,
            'esta_activo' => 1
        ]);

        $grupo->materias()->attach($request->materias);

        return redirect()->route('grupos.index')
                         ->with('success', 'Grupo registrado con éxito. Ahora matricule alumnos.');
    }

    /**
     * Muestra la lista para matricular alumnos (Filtra por carrera).
     */
    public function show(string $id)
    {
        // Esto está correcto: Carga 'materias' (plural) y el historial
        $grupo = Grupo::with([
            'materias',
            'cuatrimestre',
            'carrera',
            'alumnos',
            'grupoAnterior', // <-- Historial
            'grupoSiguiente'  // <-- Historial
        ])->findOrFail($id);

        $alumnos_disponibles = Alumno::where('carrera_id', $grupo->carrera_id)
                                        ->where('esta_activo', 1)
                                        ->get();

        $alumnos_matriculados_ids = $grupo->alumnos->pluck('id')->toArray();

        return view('grupos.show', compact('grupo', 'alumnos_disponibles', 'alumnos_matriculados_ids'));
    }

    /**
     * Procesa la solicitud para asignar alumnos a un grupo.
     */
    public function assignStudents(Request $request, string $id)
    {
        // (Este método se queda igual, es correcto)
        $grupo = Grupo::findOrFail($id);

        $request->validate([
            'alumnos_ids' => 'nullable|array',
            'alumnos_ids.*' => 'exists:alumnos,id',
        ]);

        $grupo->alumnos()->sync($request->input('alumnos_ids', []));

        return redirect()->route('grupos.show', $grupo->id)
                         ->with('success', 'Alumnos matriculados en el grupo correctamente.');
    }

    /**
     * Muestra el formulario para editar un grupo específico.
     */
    public function edit(Grupo $grupo)
    {
        // (Este método se queda igual, es correcto)
        $carreras = Carrera::where('esta_activo', 1)->get();
        $cuatrimestres = Cuatrimestre::where('esta_activo', 1)->get();

        $materias_de_la_carrera = $grupo->carrera->materias()
                                        ->where('materias.esta_activo', 1)
                                        ->get();

        $materias_actuales_ids = $grupo->materias()->pluck('materias.id')->toArray();

        return view('grupos.edit', compact('grupo', 'carreras', 'cuatrimestres', 'materias_de_la_carrera', 'materias_actuales_ids'));
    }

    /**
     * Actualiza el grupo en la base de datos (Usado para Edición y Reactivación).
     */
    public function update(Request $request, Grupo $grupo)
    {
        // (Este método se queda igual, es correcto)
        $request->validate([
            'nombre' => 'required|max:255|unique:grupos,nombre,' . $grupo->id,
            'materias' => 'required|array',
            'materias.*' => 'exists:materias,id',
            'cuatrimestre_id' => 'required|exists:cuatrimestres,id',
            'carrera_id' => 'required|exists:carreras,id',
            'esta_activo' => 'boolean',
        ]);

        $data = $request->only(['nombre', 'cuatrimestre_id', 'carrera_id']);
        $data['esta_activo'] = $request->boolean('esta_activo');

        $grupo->update($data);

        $grupo->materias()->sync($request->materias);

        return redirect()->route('grupos.index')
                         ->with('success', 'Grupo actualizado con éxito.');
    }

    /**
     * Desactiva (Elimina Suavemente) un grupo (usado por el botón 'Desactivar').
     */
    public function destroy(Grupo $grupo)
    {
        // (Este método se queda igual, es correcto)
        $grupo->update(['esta_activo' => false]);

        return redirect()->route('grupos.index')
                         ->with('success', 'Grupo desactivado con éxito.');
    }

    // --- NUEVOS MÉTODOS (Respuesta al feedback de tu maestra) ---

    /**
     * Muestra el formulario para "Promover" un grupo al siguiente cuatrimestre.
     */
    public function showPromoverForm(Grupo $grupo)
    {
        // (Este método se queda igual, es correcto)
        $cuatrimestres_siguientes = Cuatrimestre::where('fecha_inicio', '>', $grupo->cuatrimestre->fecha_fin)
                                              ->where('esta_activo', 1)
                                              ->get();

        if($cuatrimestres_siguientes->isEmpty()) {
            $cuatrimestres_siguientes = Cuatrimestre::where('esta_activo', 1)
                                                    ->where('id', '!=', $grupo->cuatrimestre_id)
                                                    ->get();
        }

        return view('grupos.promover', compact('grupo', 'cuatrimestres_siguientes'));
    }

    /**
     * Procesa la "promoción" del grupo.
     */
    public function promover(Request $request, Grupo $grupo)
    {
        // (Este método se queda igual, es correcto)
        $request->validate([
            'nombre' => 'required|string|max:255|unique:grupos,nombre',
            'cuatrimestre_id' => 'required|exists:cuatrimestres,id'
        ]);

        $nuevoGrupo = Grupo::create([
            'nombre' => $request->nombre,
            'carrera_id' => $grupo->carrera_id, // Hereda la carrera
            'cuatrimestre_id' => $request->cuatrimestre_id,
            'grupo_anterior_id' => $grupo->id, // ¡Aquí guardamos el historial!
            'esta_activo' => 1
        ]);

        $alumnosIds = $grupo->alumnos()->pluck('alumnos.id');
        if ($alumnosIds->isNotEmpty()) {
            $nuevoGrupo->alumnos()->attach($alumnosIds);
        }

        $materiasIds = $grupo->materias()->pluck('materias.id');
        if ($materiasIds->isNotEmpty()) {
            $nuevoGrupo->materias()->attach($materiasIds);
        }

        $grupo->update(['esta_activo' => 0]);

        return redirect()->route('grupos.index')
                         ->with('success', 'Grupo promovido a ' . $nuevoGrupo->nombre . ' exitosamente.');
    }


    // ==========================================================
    // --- MÉTODO PARA LA API DE JAVASCRIPT ---
    // ==========================================================

    /**
     * Devuelve las materias de una carrera específica como JSON.
     */
    public function getMateriasPorCarrera(Carrera $carrera)
    {
        // (Este método se queda igual, es correcto)
        $materias = $carrera->materias()
                            ->where('materias.esta_activo', 1)
                            ->get(['materias.id', 'materias.nombre']); // Solo devolvemos ID y Nombre

        return response()->json($materias);
    }
}
