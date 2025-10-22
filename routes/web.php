<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\CicloEscolarController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\CuatrimestreController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación
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

// Rutas protegidas por autenticación
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
        
        // Ruta de cierre de sesión
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });


Route::resource('carreras', CarreraController::class);
Route::resource('ciclos', CicloEscolarController::class);
Route::resource('alumnos', AlumnoController::class);
Route::resource('materias', MateriaController::class);
Route::resource('cuatrimestres', CuatrimestreController::class);
Route::resource('grupos', GrupoController::class);
Route::post('grupos/{grupo}/assign-students', [GrupoController::class, 'assignStudents'])
    ->name('grupos.assign_students');
