<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; 

class Materia extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'objetivo',
        'esta_activo'
    ];

    protected $casts = [
        'esta_activo' => 'boolean',
    ];


    public function unidades()
    {
        return $this->hasMany(Unidad::class);
    }

    public function carreras()
    {
        return $this->belongsToMany(Carrera::class, 'carrera_materia', 'materia_id', 'carrera_id');
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_materia');
    }



    // prueba 1:
    public function getCodigo(): string
    {
        return Str::upper(Str::substr($this->nombre, 0, 3));
    }

    // prueba 2:
    public function getStatusTexto(): string
    {
        if ($this->esta_activo) {
            return "Activo";
        } else {
            return "Inactivo";
        }
    }

    // prueba 3:
    public function getObjetivoCorto(int $longitud = 10): string
    {
        return Str::limit($this->objetivo, $longitud);
    }
}