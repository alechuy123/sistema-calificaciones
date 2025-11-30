<?php

namespace App\Http\Controllers;

// ... (tus 'use' statements)
use App\Models\Unidad;
use App\Models\Instrumento;
use App\Models\Materia;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ConfiguracionEvaluacionController extends Controller
{
    public function show(Materia $materia)
    {
        // Cargamos las unidades y sus instrumentos
        $materia->load('unidades.instrumentos'); 
        return view('evaluacion.configurar', compact('materia'));
    }


    public function store(Request $request, Materia $materia)
    {
        // 1. VALIDACIÓN (Esto ya estaba correcto)
        $request->validate([
            'unidades' => 'required|array',
            'unidades.*.fecha_inicio' => 'nullable|date',
            'unidades.*.fecha_fin' => 'nullable|date|after_or_equal:unidades.*.fecha_inicio',
            'unidades.*.instrumentos' => 'sometimes|required|array|min:1',
            'unidades.*.instrumentos.*.nombre' => 'required_with:unidades.*.instrumentos|string|max:255',
            'unidades.*.instrumentos.*.porcentaje' => 'required_with:unidades.*.instrumentos|integer|min:1|max:100',
            'unidades.*.objetivo' => 'nullable|string|max:1000', // El objetivo se valida
        ], [
            'unidades.*.fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.'
        ]);


        // 2. VALIDACIÓN DEL 100% (Esto ya estaba correcto)
        foreach ($request->unidades as $unidadId => $unidadData) {
            $totalPorcentaje = 0;
            if (isset($unidadData['instrumentos'])) {
                foreach ($unidadData['instrumentos'] as $instrumento) {
                    $totalPorcentaje += (int)$instrumento['porcentaje'];
                }
                if ($totalPorcentaje !== 100) {
                    $unidadNombre = Unidad::find($unidadId)->nombre ?? "ID $unidadId";
                    return redirect()->back()->withErrors([
                        "unidad_porcentaje" => "La suma de porcentajes en la Unidad '{$unidadNombre}' (Total: {$totalPorcentaje}%) debe ser exactamente 100%."
                    ])->withInput();
                }
            }
        }

        // 3. GUARDAR EN BASE DE DATOS
        DB::transaction(function () use ($request, $materia) {
            
            if ($request->has('instrumentos_a_eliminar')) {
                Instrumento::whereIn('id', $request->input('instrumentos_a_eliminar'))->delete();
            }
            
            foreach ($request->unidades as $unidadId => $unidadData) {
                $unidad = Unidad::findOrFail($unidadId);

                // --- !!! AQUÍ ESTÁ LA CORRECCIÓN !!! ---
                // Actualizamos las fechas Y EL OBJETIVO de la Unidad
                $unidad->update([
                    'fecha_inicio' => $unidadData['fecha_inicio'],
                    'fecha_fin' => $unidadData['fecha_fin'],
                    'objetivo' => $unidadData['objetivo'] ?? null // <-- ¡¡AQUÍ FALTABA EL OBJETIVO!!
                ]);
                // --- !!! FIN DE LA CORRECCIÓN !!! ---
                
                
                // (Lógica existente para guardar instrumentos)
                if (isset($unidadData['instrumentos'])) {
                    foreach ($unidadData['instrumentos'] as $instrumentoData) {
                        
                        if (isset($instrumentoData['id']) && !empty($instrumentoData['id'])) {
                            Instrumento::where('id', $instrumentoData['id'])->update([
                                'nombre' => $instrumentoData['nombre'],
                                'porcentaje' => $instrumentoData['porcentaje'],
                            ]);
                        } else {
                            $unidad->instrumentos()->create([
                                'nombre' => $instrumentoData['nombre'],
                                'porcentaje' => $instrumentoData['porcentaje'],
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()->route('materias.index')->with('success', 'Estructura de evaluación y periodos guardados con éxito.');
    }
}