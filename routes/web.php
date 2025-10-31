<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\CicloEscolarController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\CuatrimestreController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\CriterioController;
use App\Http\Controllers\CalificacionController;
// (V1) Controlador de tu primer proyecto
use App\Http\Controllers\ConfiguracionEvaluacionController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas comunes de ambos proyectos (del proyecto V2)
Route::resource('carreras', CarreraController::class);
Route::resource('ciclos', CicloEscolarController::class);
Route::resource('alumnos', AlumnoController::class);
Route::resource('materias', MateriaController::class); // Esto cubre create, store, edit, update, destroy, index
Route::resource('cuatrimestres', CuatrimestreController::class);
Route::resource('grupos', GrupoController::class);
Route::post('grupos/{grupo}/assign-students', [GrupoController::class, 'assignStudents'])
    ->name('grupos.assign_students');


// --- RUTAS DEL PROYECTO ANTIGUO (V1) AÑADIDAS ---

// Rutas para Criterios de Evaluación (Ya estaban en V2)
Route::get('criterios/create/{materia?}', [CriterioController::class, 'create'])->name('criterios.create');
Route::resource('criterios', CriterioController::class)->except(['create', 'show']);

// Rutas para Calificaciones (Ya estaban en V2)
Route::get('/calificar/{grupo}/{materia}', [CalificacionController::class, 'create'])->name('calificaciones.create');
Route::post('/calificar', [CalificacionController::class, 'store'])->name('calificaciones.store');

// (V1) Rutas específicas de tu primer proyecto
Route::get('/materias/{materia}/configurar-evaluacion', [ConfiguracionEvaluacionController::class, 'show'])->name('evaluacion.show');
Route::post('/materias/{materia}/configurar-evaluacion', [ConfiguracionEvaluacionController::class, 'store'])->name('evaluacion.store');
Route::get('/materias/{materia}/info', [MateriaController::class, 'showInfoPublica'])->name('materia.publica.info');