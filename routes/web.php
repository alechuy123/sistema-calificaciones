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
// Se eliminó CriterioController ya que no se usa
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\AuthController;
// Import de Dafne añadido:
use App\Http\Controllers\ConfiguracionEvaluacionController;
// --- Fin Imports ---


Route::get('/', function () {
    return redirect()->route('login');
});


// --- Rutas de Autenticación de Heri (para invitados) ---
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


    // --- INICIO: Rutas de Calificación (Tu nueva idea + GERA) ---
    
    // 1. Ruta para MOSTRAR el "Selector" de 3 dropdowns (Flujo original)
    Route::get('/calificar/seleccionar', [CalificacionController::class, 'showSelector'])
         ->name('calificaciones.selector');
         
    // 1.B. NUEVA RUTA: Selector iniciando desde MATERIA (Flujo nuevo)
    // Esta es la ruta que usa el botón "Calificar" en la lista de materias
    // URL: /calificaciones/por-materia/5
    Route::get('/calificaciones/por-materia/{materia}', [CalificacionController::class, 'showSelectorPorMateria'])
         ->name('calificaciones.por_materia'); // <<< RUTA NUEVA AGREGADA AQUÍ >>>
    
    // 2. Ruta para MOSTRAR la hoja de calificación (la tabla HTML)
    // URL: /grupos/1/materias/5/unidades/8/calificar
    Route::get('/grupos/{grupo}/materias/{materia}/unidades/{unidad}/calificar', [CalificacionController::class, 'showHojaDeCalificacion'])
         ->name('calificaciones.hoja');
    
    // 3. Ruta para GUARDAR (vía JS/Fetch) una calificación de la hoja
    Route::post('/calificaciones/guardar-unidad', [CalificacionController::class, 'storeOrUpdate'])
         ->name('calificaciones.guardar.unidad');
    
    // --- FIN: Rutas de Calificación ---


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

    // API para JavaScript (Formulario de Alumnos)
    Route::get('/api/carreras/{carrera}/grupos', [AlumnoController::class, 'getGruposPorCarrera'])
          ->name('api.carreras.grupos');

    // API para el "Selector" de Calificaciones
    Route::get('/api/grupos/{grupo}/materias', [CalificacionController::class, 'getMateriasPorGrupo'])
         ->name('api.grupos.materias');
    
    Route::get('/api/materias/{materia}/unidades', [CalificacionController::class, 'getUnidadesPorMateria'])
         ->name('api.materias.unidades');

});