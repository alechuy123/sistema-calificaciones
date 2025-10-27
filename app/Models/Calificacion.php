<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    use HasFactory;
    
    protected $table = 'calificacions';

    protected $fillable = [
        'alumno_id',
        'grupo_id',
        'criterio_evaluacion_id',
        'subtarea_id',
        'puntuacion_decimal',
    ];

    
    /**
     * Una Calificación pertenece a un Alumno.
     */
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    /**
     * Una Calificación pertenece a un Grupo.
     */
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    /**
     * Una Calificación pertenece a un Criterio de Evaluación.
     */
    public function criterioEvaluacion()
    {
        return $this->belongsTo(CriterioEvaluacion::class, 'criterio_evaluacion_id');
    }

    /**
     * Una Calificación PUEDE pertenecer a una Subtarea.
     */
    public function subtarea()
    {
        return $this->belongsTo(Subtarea::class, 'subtarea_id');
    }
}
