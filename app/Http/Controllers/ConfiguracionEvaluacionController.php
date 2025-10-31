<?php

namespace App\Http\Controllers;

// ... (tus 'use' statements)
use App\Models\Unidad; // Asegúrate de que 'Unidad' esté importado
use App\Models\Instrumento; 
use App\Models\Materia; 
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ConfiguracionEvaluacionController extends Controller
{
    // ... (Tu método 'show' se queda igual) ...
    public function show(Materia $materia)
    {
        $materia->load('unidades.instrumentos');
        return view('evaluacion.configurar', compact('materia'));
    }


    public function store(Request $request, Materia $materia)
    {
        // 1. ACTUALIZAR VALIDACIÓN
        $request->validate([
            'unidades' => 'required|array',
            
            // --- NUEVAS REGLAS PARA FECHAS ---
            // 'unidades.*.fecha_inicio' valida la fecha_inicio dentro de cada unidad del array
            'unidades.*.fecha_inicio' => 'required|date',
            'unidades.*.fecha_fin' => 'required|date|after_or_equal:unidades.*.fecha_inicio',
            
            // --- REGLAS EXISTENTES ---
            'unidades.*.instrumentos' => 'sometimes|required|array|min:1', // 'sometimes' por si solo guardan fechas
            'unidades.*.instrumentos.*.nombre' => 'required_with:unidades.*.instrumentos|string|max:255',
            'unidades.*.instrumentos.*.porcentaje' => 'required_with:unidades.*.instrumentos|integer|min:1|max:100',
        ], [
            // Mensajes de error personalizados
            'unidades.*.fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.'
        ]);


        // 2. VALIDACIÓN DEL 100% (Modificada para checar si existen instrumentos)
        foreach ($request->unidades as $unidadId => $unidadData) {
            $totalPorcentaje = 0;
            
            // Solo validamos el 100% SI se enviaron instrumentos para esta unidad
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
            
            // (Lógica existente para eliminar)
            if ($request->has('instrumentos_a_eliminar')) {
                Instrumento::whereIn('id', $request->input('instrumentos_a_eliminar'))->delete();
            }
            
            foreach ($request->unidades as $unidadId => $unidadData) {
                $unidad = Unidad::findOrFail($unidadId);

                // --- !!! LÓGICA NUEVA PARA GUARDAR FECHAS !!! ---
                // Actualizamos las fechas de la Unidad
                $unidad->update([
                    'fecha_inicio' => $unidadData['fecha_inicio'],
                    'fecha_fin' => $unidadData['fecha_fin'],
                ]);
                // --- !!! FIN DE LÓGICA NUEVA !!! ---
                
                
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