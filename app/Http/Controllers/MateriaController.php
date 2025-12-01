<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materia;
use App\Models\Carrera;
use App\Models\Unidad;
use Illuminate\Support\Facades\DB;

class MateriaController extends Controller
{
    /**
     * Muestra la lista de materias (precargando carreras y unidades)
     */
    public function index(Request $request)
    {
        $query = Materia::query();

        $filtro = $request->get('filtro');

        if ($filtro == 'activas') {
            $query->where('esta_activo', true);
        } elseif ($filtro == 'desactivadas') {
            $query->where('esta_activo', false);
        }

        $materias = $query->get();

        return view('materias.index', compact('materias'));
    }

    /**
     * Muestra el formulario para crear.
     */
    public function create()
    {
        $carreras = Carrera::all();
        return view('materias.create', compact('carreras'));
    }

    /**
     * Guarda una nueva materia.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:materias|max:255',
            'objetivo' => 'required|string',
            'carreras' => 'required|array|min:1',
            'carreras.*' => 'exists:carreras,id',
            'unidades' => 'required|array|min:1',
            'unidades.*.nombre' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $materia = Materia::create([
                'nombre' => $request->nombre,
                'objetivo' => $request->objetivo,
            ]);

            $materia->carreras()->attach($request->input('carreras'));

            foreach ($request->unidades as $unidadData) {
                $materia->unidades()->create([
                    'nombre' => $unidadData['nombre'],
                ]);
            }
        });

        return redirect()->route('materias.index')
                         ->with('success', 'Materia, unidades y carreras configuradas correctamente.');
    }

    public function show(string $id)
    {
        // ...
    }

    /**
     * Muestra el formulario para editar.
     */
    public function edit(Materia $materia)
    {
        $materia->load('unidades');
        $carreras = Carrera::all();
        return view('materias.edit', compact('materia', 'carreras'));
    }

    /**
     * Actualiza la materia.
     */
    public function update(Request $request, Materia $materia)
    {
        // --- CORRECCIÓN PARA EL BOTÓN "ACTIVAR" ---
        // Si el formulario NO envía 'carreras', asumimos que es una reactivación rápida desde el index.
        if (!$request->has('carreras')) {
            // Solo actualizamos el estado
            $materia->update(['esta_activo' => 1]);

            return redirect()->route('materias.index')
                             ->with('success', 'Materia reactivada correctamente.');
        }
        // ------------------------------------------


        // Si SÍ trae carreras, hacemos la validación completa de edición
        $request->validate([
            'nombre' => 'required|max:255|unique:materias,nombre,' . $materia->id,
            'objetivo' => 'required|string',
            'carreras' => 'required|array',
            'carreras.*' => 'exists:carreras,id',
            'unidades' => 'required|array|min:1',
            'unidades.*.nombre' => 'required|string|max:255',
            'unidades.*.id' => 'sometimes|nullable|integer|exists:unidads,id',
            'esta_activo' => 'boolean',
        ]);

        DB::transaction(function () use ($request, $materia) {

            $data = $request->only(['nombre', 'objetivo']);
            $data['esta_activo'] = $request->boolean('esta_activo');
            $materia->update($data);

            $materia->carreras()->sync($request->input('carreras'));

            $unidadesExistentesIds = [];
            foreach ($request->unidades as $unidadData) {
                if (is_numeric($unidadData['id'] ?? null) && (int)$unidadData['id'] > 0) {

                    $materia->unidades()->where('id', $unidadData['id'])->update(['nombre' => $unidadData['nombre']]);
                    $unidadesExistentesIds[] = (int)$unidadData['id'];
                } else {

                    $nuevaUnidad = $materia->unidades()->create(['nombre' => $unidadData['nombre']]);
                    $unidadesExistentesIds[] = $nuevaUnidad->id;
                }
            }

            $materia->unidades()->whereNotIn('id', $unidadesExistentesIds)->delete();
        });

        return redirect()->route('materias.index')
                         ->with('success', 'Materia actualizada correctamente.');
    }


    public function destroy(Materia $materia)
    {
        $materia->update(['esta_activo' => false]);

        return redirect()->route('materias.index')
                         ->with('success', 'Materia desactivada con éxito.');
    }

    public function showInfoPublica(Materia $materia)
    {
        $materia->load('unidades.instrumentos');
        return view('materias.info_publica', compact('materia'));
    }
}
