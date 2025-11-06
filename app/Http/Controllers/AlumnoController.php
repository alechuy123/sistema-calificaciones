<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use Illuminate\Http\Request;
use App\Models\Grupo; // Asegúrate de que este import exista

class AlumnoController extends Controller
{
    /**
     * Muestra la lista de todos los alumnos (activos e inactivos).
     */
    public function index()
    {
        // --- ¡CAMBIO AQUÍ! ---
        // Le decimos a Eloquent que también traiga la relación 'grupos'
        // Esto es para poder mostrar el grupo en el listado (index).
        $alumnos = Alumno::with(['carrera', 'cicloEscolar', 'grupos'])->get();
        // --- FIN DEL CAMBIO ---

        return view('alumnos.index', compact('alumnos'));
    }

    /**
     * Muestra el formulario para crear un nuevo alumno.
     */
    public function create()
    {
        // Esto está correcto, solo pasamos carreras y ciclos.
        // Los grupos se cargan con JS.
        $carreras = Carrera::where('esta_activo', 1)->get();
        $ciclos = CicloEscolar::where('esta_activo', true)->get();

        return view('alumnos.create', compact('carreras', 'ciclos'));
    }

    /**
     * Guarda un nuevo alumno en la base de datos.
     */
    public function store(Request $request)
    {
        // Esta lógica ya está correcta.
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'matricula' => 'required|unique:alumnos|max:20',
            'carrera_id' => 'required|exists:carreras,id',
            'ciclo_escolar_id' => 'required|exists:ciclo_escolars,id',
            'grupo_id' => 'nullable|exists:grupos,id',
        ]);

        $alumno = Alumno::create($request->except('grupo_id'));

        if ($request->filled('grupo_id')) {
            $alumno->grupos()->attach($request->grupo_id);
        }

        return redirect()->route('alumnos.index')
                         ->with('success', 'Alumno registrado con éxito.');
    }

    public function show(string $id)
    {
        // No es necesario para el CRUD de gestión simple
    }

    /**
     * Muestra el formulario para editar un alumno específico.
     * (Este método ya tiene la lógica del JS que hicimos)
     */
    public function edit(Alumno $alumno)
    {
        $carreras = Carrera::where('esta_activo', 1)->get();
        $ciclos = CicloEscolar::where('esta_activo', 1)->get();

        // 1. Obtenemos los grupos de la carrera actual (para el JS)
        $grupos_de_la_carrera = Grupo::where('carrera_id', $alumno->carrera_id)
                                    ->where('esta_activo', 1)
                                    ->get();

        // 2. Obtenemos el ID del grupo actual
        $grupo_actual_id = $alumno->grupos()->first()->id ?? null;

        return view('alumnos.edit', compact(
            'alumno',
            'carreras',
            'ciclos',
            'grupos_de_la_carrera',
            'grupo_actual_id'
        ));
    }

    /**
     * Actualiza el alumno en la base de datos.
     * (Este método ya tiene la lógica de 'sync' que hicimos)
     */
    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'matricula' => 'required|max:20|unique:alumnos,matricula,' . $alumno->id,
            'carrera_id' => 'required|exists:carreras,id',
            'ciclo_escolar_id' => 'required|exists:ciclo_escolars,id',
            'esta_activo' => 'boolean',
            'grupo_id' => 'nullable|exists:grupos,id',
        ]);

        $data = $request->except(['_token', '_method', 'grupo_id']);
        $data['esta_activo'] = $request->boolean('esta_activo');

        $alumno->update($data);

        // Sincroniza el grupo
        if ($request->has('grupo_id')) {
            $grupo_id = $request->input('grupo_id');
            $alumno->grupos()->sync($grupo_id ? [$grupo_id] : []);
        }

        return redirect()->route('alumnos.index')
                         ->with('success', 'Alumno actualizado con éxito.');
    }

    /**
     * Desactiva un alumno.
     * (Este método ya tiene la lógica de 'sync' que hicimos)
     */
    public function destroy(Alumno $alumno)
    {
        $alumno->grupos()->sync([]); // Lo quita de cualquier grupo
        $alumno->update(['esta_activo' => false]);

        return redirect()->route('alumnos.index')
                         ->with('success', 'Alumno desactivado y desvinculado de grupos con éxito.');
    }


    /**
     * API para el JavaScript.
     * (Este método ya está correcto)
     */
    public function getGruposPorCarrera(Carrera $carrera)
    {
        $grupos = $carrera->grupos()
                         ->where('grupos.esta_activo', 1)
                         ->get(['grupos.id', 'grupos.nombre']);

        return response()->json($grupos);
    }
}

