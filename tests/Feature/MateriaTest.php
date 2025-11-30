<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Materia; 

class MateriaTest extends TestCase
{
    
    // prueba 1: 
    public function test_get_codigo(): void
    {
        $materia = new Materia(['nombre' => 'Cálculo']);
        $codigo = $materia->getCodigo();
        $this->assertEquals('CÁL', $codigo);
    }

    // prueba 2:
    public function test_get_status_texto_activa(): void
    {
        $materia = new Materia(['esta_activo' => true]);
        $status = $materia->getStatusTexto();
        $this->assertEquals('Activo', $status);
    }

    // prueba 3:
    public function test_get_objetivo_corto(): void
    {
        $materia = new Materia(['objetivo' => 'Este es el objetivo principal de la materia.']);
        $objetivoCorto = $materia->getObjetivoCorto(10);
        $this->assertEquals('Este es el...', $objetivoCorto);
    }
}
