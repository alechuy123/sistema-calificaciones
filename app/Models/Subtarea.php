<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subtarea extends Model
{
    use HasFactory;
    
    // Añadido para que coincida con la migración
    protected $fillable = [
        'nombre',
        'instrumento_id',
    ];

    public function instrumento()
    {
        return $this->belongsTo(Instrumento::class);
    }
}