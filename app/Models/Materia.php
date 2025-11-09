<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// --- ¡ESTAS LÍNEAS FALTABAN! ---
use App\Models\Unidad;
use App\Models\Carrera;
use App\Models\Grupo;

class Materia extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     * (Esto se queda igual)
     */
    protected $fillable = [
        'nombre',
        'objetivo',
        'esta_activo'
    ];

    /**
     * Asegura que 'esta_activo' sea tratado como booleano (true/false).
     * (Esto se queda igual)
     */
    protected $casts = [
        'esta_activo' => 'boolean',
    ];

    // --- RELACIONES COMBINADAS ---

    /**
     * RELACIÓN V1: Una Materia tiene muchas Unidades.
     * (Esto se queda igual)
     */
    public function unidades()
    {
        return $this->hasMany(Unidad::class);
    }

    /**
     * RELACIÓN V2: Una Materia pertenece a muchas Carreras.
     * (Esto se queda igual)
     */
    public function carreras()
    {
        return $this->belongsToMany(Carrera::class, 'carrera_materia', 'materia_id', 'carrera_id');
    }

    /**
     * --- CAMBIO 1 ---
     * RELACIÓN V2: Una Materia ahora puede estar en MUCHOS Grupos.
     * Se cambia de 'hasMany' a 'belongsToMany'.
     */
    public function grupos()
    {
        // return $this->hasMany(Grupo::class, 'materia_id'); // <- CÓDIGO ANTIGUO
        return $this->belongsToMany(Grupo::class, 'grupo_materia'); // <- CÓDIGO NUEVO
    }
}