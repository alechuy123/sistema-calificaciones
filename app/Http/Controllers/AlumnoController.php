<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use Illuminate\Http\Request;
use App\Models\Grupo;

class AlumnoController extends Controller
{
    public function index()
    {
        $alumnos = Alumno::with(['carrera', 'cicloEscolar', 'grupos'])->get();
        return view('alumnos.index', compact('alumnos'));
    }

    public function create()
    {
        $carreras = Carrera::where('esta_activo', 1)->get();
        $ciclos = CicloEscolar::where('esta_activo', true)->get();
        return view('alumnos.create', compact('carreras', 'ciclos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            // CAMBIO: Se cambió 'required' por 'nullable'
            'matricula' => 'nullable|unique:alumnos|max:20',
            'carrera_id' => 'required|exists:carreras,id',
            'ciclo_escolar_id' => 'required|exists:ciclo_escolars,id',
            'grupo_id' => 'nullable|exists:grupos,id',
        ]);

        $alumno = Alumno::create($request->except('grupo_id'));

        if ($request->filled('grupo_id')) {
            $grupo = Grupo::find($request->grupo_id);
            if ($grupo) {
                $grupo->alumnos()->attach($alumno->id);
            }
        }

        return redirect()->route('alumnos.index')
                         ->with('success', 'Alumno registrado con éxito.');
    }

    public function show(string $id)
    {
        // No necesario
    }

    public function edit(Alumno $alumno)
    {
        $carreras = Carrera::where('esta_activo', 1)->get();
        $ciclos = CicloEscolar::where('esta_activo', 1)->get();

        $grupos_de_la_carrera = Grupo::where('carrera_id', $alumno->carrera_id)
                                    ->where('esta_activo', 1)
                                    ->get();

        $grupo_actual_id = $alumno->grupos()->first()->id ?? null;

        return view('alumnos.edit', compact(
            'alumno',
            'carreras',
            'ciclos',
            'grupos_de_la_carrera',
            'grupo_actual_id'
        ));
    }

    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            // CAMBIO: Se cambió 'required' por 'nullable'
            'matricula' => 'nullable|max:20|unique:alumnos,matricula,' . $alumno->id,
            'carrera_id' => 'required|exists:carreras,id',
            'ciclo_escolar_id' => 'required|exists:ciclo_escolars,id',
            'esta_activo' => 'boolean',
            'grupo_id' => 'nullable|exists:grupos,id',
        ]);

        $data = $request->except(['_token', '_method', 'grupo_id']);
        $data['esta_activo'] = $request->boolean('esta_activo');

        $alumno->update($data);

        if ($request->has('grupo_id')) {
            $grupo_id = $request->input('grupo_id');
            $alumno->grupos()->sync($grupo_id ? [$grupo_id] : []);
        }

        return redirect()->route('alumnos.index')
                         ->with('success', 'Alumno actualizado con éxito.');
    }

    public function destroy(Alumno $alumno)
    {
        $alumno->grupos()->sync([]);
        $alumno->update(['esta_activo' => false]);

        return redirect()->route('alumnos.index')
                         ->with('success', 'Alumno desactivado con éxito.');
    }

    public function getGruposPorCarrera(Carrera $carrera)
    {
        $grupos = $carrera->grupos()
                         ->where('grupos.esta_activo', 1)
                         ->get(['grupos.id', 'grupos.nombre']);

        return response()->json($grupos);
    }
}
