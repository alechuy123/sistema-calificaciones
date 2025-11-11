<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unidad extends Model
{
    use HasFactory;
    
    protected $table = 'unidads'; 

    /**
     * Actualizamos el $fillable
     */
    protected $fillable = [
        'nombre',
        'materia_id',
        'objetivo',
        'fecha_inicio', 
        'fecha_fin',    
    ];

    /**
     * (Recomendado) Añadimos $casts para que Laravel trate esto como objetos de fecha
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    /**
     * Relación: Una Unidad pertenece a una Materia
     */
    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    /**
     * Relación: Una Unidad tiene muchos Instrumentos
     */
    public function instrumentos()
    {
        return $this->hasMany(Instrumento::class);
    }
}