<?php

namespace App\Http\Controllers;

use App\Models\CriterioEvaluacion;
use App\Models\Materia;
use App\Models\Subtarea; 
use Illuminate\Http\Request;

class CriterioController extends Controller
{
    // ... (tus métodos create y store se mantienen igual) ...
    public function create(Materia $materia = null)
    {
        $materias = Materia::all();
        return view('criterios.create', compact('materias', 'materia'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'grupo_id' => 'required|exists:grupos,id',
            'nombre' => 'required|string|max:255',
            'porcentaje_decimal' => 'required|numeric|min:0',
            'subtareas.*.nombre' => 'nullable|string|max:255',
        ]);

        $totalPorcentaje = CriterioEvaluacion::where('grupo_id', $request->grupo_id)->sum('porcentaje_decimal');
        if (($totalPorcentaje + $request->porcentaje_decimal) > 100) {
            return back()->withErrors(['porcentaje_decimal' => 'La suma de porcentajes para este grupo no puede superar el 100%.'])->withInput();
        }

        $criterio = CriterioEvaluacion::create($request->only(['materia_id', 'grupo_id', 'nombre', 'porcentaje_decimal']));

        if ($request->has('subtareas')) {
            foreach ($request->subtareas as $subtareaData) {
                if (!empty($subtareaData['nombre'])) {
                    $criterio->subtareas()->create($subtareaData);
                }
            }
        }

        return redirect()->route('calificaciones.create', [
            'grupo' => $request->grupo_id,
            'materia' => $request->materia_id
        ])->with('success', 'Criterio creado exitosamente.');
    }


    public function edit(CriterioEvaluacion $criterio)
    {
        // Se carga el criterio con sus subtareas para la vista de edición.
        $criterio->load('subtareas');
        return view('criterios.edit', compact('criterio'));
    }

    public function update(Request $request, CriterioEvaluacion $criterio)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'porcentaje_decimal' => 'required|numeric|min:0',
            'subtareas.*.nombre' => 'nullable|string|max:255', // Se valida el nombre de las subtareas
        ]);
        
        // (Aquí va tu validación del 100%)
        $totalPorcentaje = CriterioEvaluacion::where('grupo_id', $criterio->grupo_id)->where('id', '!=', $criterio->id)->sum('porcentaje_decimal');
        if (($totalPorcentaje + $request->porcentaje_decimal) > 100) {
            return back()->withErrors(['porcentaje_decimal' => 'La suma de porcentajes para este grupo no puede superar el 100%.'])->withInput();
        }

        // 1. Actualiza el criterio principal
        $criterio->update($request->only('nombre', 'porcentaje_decimal'));

        // 2. Procesa las subtareas a ELIMINAR
        if ($request->has('delete_subtareas')) {
            Subtarea::destroy($request->delete_subtareas);
        }

        // 3. Procesa las subtareas a ACTUALIZAR o CREAR
        if ($request->has('subtareas')) {
            foreach ($request->subtareas as $id => $data) {
                // Si el ID es un número, es una subtarea existente -> ACTUALIZAR
                if (is_numeric($id)) {
                    Subtarea::find($id)->update($data);
                } 
                // Si el ID no es numérico (ej. 'new_12345'), es una nueva -> CREAR
                elseif (strpos($id, 'new_') === 0 && !empty($data['nombre'])) {
                    $criterio->subtareas()->create($data);
                }
            }
        }
    
        // Lógica de redirección inteligente
        if ($request->has('grupo_id')) {
            return redirect()->route('calificaciones.create', [
                'grupo' => $request->grupo_id,
                'materia' => $criterio->materia_id
            ])->with('success', 'Criterio y subtareas actualizados exitosamente.');
        }

        return back()->with('success', 'Criterio y subtareas actualizados exitosamente.');
    }

    public function destroy(CriterioEvaluacion $criterio)
    {
        $criterio->delete();
        return back()->with('success', 'Criterio eliminado exitosamente.');
    }
}
