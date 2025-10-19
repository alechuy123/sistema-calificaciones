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


Route::get('/', function () {
    return view('welcome');
});

// Rutas comunes de ambos proyectos
Route::resource('carreras', CarreraController::class);
Route::resource('ciclos', CicloEscolarController::class);
Route::resource('alumnos', AlumnoController::class);
Route::resource('materias', MateriaController::class);
Route::resource('cuatrimestres', CuatrimestreController::class);
Route::resource('grupos', GrupoController::class);
Route::post('grupos/{grupo}/assign-students', [GrupoController::class, 'assignStudents'])
    ->name('grupos.assign_students');


// --- NUEVAS RUTAS AÑADIDAS DEL PROYECTO ANTIGUO ---

// Rutas para Criterios de Evaluación
Route::get('criterios/create/{materia?}', [CriterioController::class, 'create'])->name('criterios.create');
Route::resource('criterios', CriterioController::class)->except(['create', 'show']);

// Rutas para Calificaciones
Route::get('/calificar/{grupo}/{materia}', [CalificacionController::class, 'create'])->name('calificaciones.create');
Route::post('/calificar', [CalificacionController::class, 'store'])->name('calificaciones.store');