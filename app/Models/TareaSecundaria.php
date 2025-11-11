<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TareaSecundaria extends Model
{
    use HasFactory;

    protected $table = 'tareasecundarias';

    protected $fillable = [
        'nombre',
        'porcentaje',
        'instrumento_id',
    ];

    public function instrumento()
    {
        return $this->belongsTo(Instrumento::class);
    }
}
