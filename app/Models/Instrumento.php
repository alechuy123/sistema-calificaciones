<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Instrumento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'porcentaje',
        'unidad_id', 
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }
    
    public function tareasecundarias()
    {
        return $this->hasMany(TareaSecundaria::class);
    }
}
