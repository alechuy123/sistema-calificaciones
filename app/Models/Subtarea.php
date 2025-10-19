<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtarea extends Model
{
    use HasFactory;
    
    protected $table = 'subtareas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        // 'valor_porcentual', // <-- SE HA ELIMINADO ESTA LÍNEA
        'criterio_evaluacion_id',
    ];

    /**
     * Una subtarea pertenece a un criterio de evaluación.
     */
    public function criterioEvaluacion()
    {
        return $this->belongsTo(CriterioEvaluacion::class, 'criterio_evaluacion_id');
    }
}
