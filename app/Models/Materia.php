<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * Se combinan los campos de ambos proyectos:
     * 'nombre' (común)
     * 'objetivo' (de tu primer código)
     * 'esta_activo' (del segundo código)
     */
    protected $fillable = [
        'nombre',
        'objetivo',
        'esta_activo'
    ];

    /**
     * Asegura que 'esta_activo' sea tratado como booleano (true/false).
     * (Del segundo código)
     */
    protected $casts = [
        'esta_activo' => 'boolean',
    ];

    // --- RELACIONES COMBINADAS ---

    /**
     * RELACIÓN V1: Una Materia tiene muchas Unidades.
     */
    public function unidades()
    {
        return $this->hasMany(Unidad::class);
    }

    /**
     * RELACIÓN V2: Una Materia pertenece a muchas Carreras.
     */
    public function carreras()
    {
        return $this->belongsToMany(Carrera::class, 'carrera_materia', 'materia_id', 'carrera_id');
    }

    /**
     * RELACIÓN V2: Una Materia puede tener varios Grupos.
     */
    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'materia_id');
    }
}