<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\CicloEscolarController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\CuatrimestreController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\DashboardController;

// --- Imports Combinados ---
use App\Http\Controllers\CriterioController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\AuthController;
// Import de Dafne añadido:
use App\Http\Controllers\ConfiguracionEvaluacionController;
// --- Fin Imports ---


Route::get('/', function () {
    return redirect()->route('login');
});


// --- Rutas de Autenticación de Heri (para invitados) ---
// --- CORRECCIÓN: Se quitó el punto extra de "Route.::" ---
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});


// --- Rutas Protegidas (¡Aquí combinamos todo!) ---
Route::middleware('auth')->group(function () {

    // Ruta de dashboard (de Heri/Alejandro)
    // CÓDIGO NUEVO:
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Ruta de cierre de sesión (de Heri)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    // --- TUS RUTAS DE GESTIÓN (Ahora protegidas) ---
    Route::resource('carreras', CarreraController::class);
    Route::resource('ciclos', CicloEscolarController::class);
    Route::resource('alumnos', AlumnoController::class);
    Route::resource('materias', MateriaController::class);
    Route::resource('cuatrimestres', CuatrimestreController::class);

    // --- Rutas de Grupos (con las nuevas funcionalidades) ---
    Route::resource('grupos', GrupoController::class);
    Route::post('grupos/{grupo}/assign-students', [GrupoController::class, 'assignStudents'])
        ->name('grupos.assign_students');

    // --- NUEVAS RUTAS AÑADIDAS (Para el plan de la maestra) ---

    // Ruta para el formulario de "Promover"
    Route::get('/grupos/{grupo}/promover', [GrupoController::class, 'showPromoverForm'])->name('grupos.promover.form');
    // Ruta que procesa la promoción
    Route::post('/grupos/{grupo}/promover', [GrupoController::class, 'promover'])->name('grupos.promover');


    // Rutas para Criterios (de Alejandro)
    Route::get('criterios/create/{materia?}', [CriterioController::class, 'create'])->name('criterios.create');
    Route::resource('criterios', CriterioController::class)->except(['create', 'show']);

    // Rutas para Calificaciones (de Alejandro)
    Route::get('/calificar/{grupo}/{materia}', [CalificacionController::class, 'create'])->name('calificaciones.create');
    Route::post('/calificar', [CalificacionController::class, 'store'])->name('calificaciones.store');

    // --- NUEVAS RUTAS DE DAFNE (Añadidas y protegidas) ---
    Route::get('/materias/{materia}/configurar-evaluacion', [ConfiguracionEvaluacionController::class, 'show'])->name('evaluacion.show');
    Route::post('/materias/{materia}/configurar-evaluacion', [ConfiguracionEvaluacionController::class, 'store'])->name('evaluacion.store');
    Route::get('/materias/{materia}/info', [MateriaController::class, 'showInfoPublica'])->name('materia.publica.info');


    // ==========================================================
    // --- RUTAS DE API PARA JAVASCRIPT ---
    // ==========================================================

    // API para JavaScript (Formulario de Grupos)
    Route::get('/api/carreras/{carrera}/materias', [GrupoController::class, 'getMateriasPorCarrera'])
         ->name('api.carreras.materias');

    // --- ¡NUEVA RUTA AÑADIDA! ---
    // API para JavaScript (Formulario de Alumnos)
    Route::get('/api/carreras/{carrera}/grupos', [AlumnoController::class, 'getGruposPorCarrera'])
         ->name('api.carreras.grupos');

});

