<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'calificaciones';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'alumno_id',
        'instrumento_id',
        'calificacion_obtenida',
    ];

    /**
     * Obtiene el alumno al que pertenece la calificación.
     */
    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    /**
     * Obtiene el instrumento de evaluación al que pertenece la calificación.
     */
    public function instrumento()
    {
        return $this->belongsTo(Instrumento::class);
    }
}