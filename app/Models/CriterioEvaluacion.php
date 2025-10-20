<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CriterioEvaluacion extends Model
{
    use HasFactory;

    protected $table = 'criterio_evaluacions';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
        'porcentaje_decimal',
        'materia_id',
        'grupo_id',
    ];

    /**
     * Un criterio tiene muchas subtareas.
     */
    public function subtareas()
    {
        return $this->hasMany(Subtarea::class, 'criterio_evaluacion_id');
    }

    /**
     * Un criterio pertenece a una materia.
     */
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    /**
     * Un criterio pertenece a un grupo.
     */
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }
}
