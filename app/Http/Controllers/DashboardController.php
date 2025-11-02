<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\Cuatrimestre;
use App\Models\Grupo;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard principal con estadísticas.
     */
    public function index()
    {
        // 1. Contamos los registros activos
        $alumnosActivos = Alumno::where('esta_activo', 1)->count();
        $gruposActivos = Grupo::where('esta_activo', 1)->count();
        $carrerasActivas = Carrera::where('esta_activo', 1)->count();

        // 2. Buscamos el cuatrimestre actual
        $cuatrimestreActual = Cuatrimestre::where('esta_activo', 1)->first();

        // 3. Pasamos los datos a la vista
        return view('welcome', [
            'alumnosActivos' => $alumnosActivos,
            'gruposActivos' => $gruposActivos,
            'carrerasActivas' => $carrerasActivas,
            'cuatrimestreActual' => $cuatrimestreActual
        ]);
    }
}
