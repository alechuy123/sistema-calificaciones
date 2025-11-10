<?php

// JESÚS ALEJANDRO OROZCO SANDOVAL

namespace Tests\Unit;


use PHPUnit\Framework\TestCase;
use App\Models\Alumno;

class AlumnoModelTest extends TestCase
{
    /**
     * Prueba que el accesor de nombre completo funciona.
     * @return void
     */
    public function test_el_accessor_de_nombre_completo_funciona(): void
    {
        // 1. PREPARACIÓN (Arrange)
        // Creamos un alumno "falso" solo en memoria (NO toca la BD)
        $alumno = new Alumno([
            'nombre' => 'Juan',
            'apellido_paterno' => 'Perez',
            'apellido_materno' => 'Gomez'
        ]);

        // 2. ACCIÓN (Act)
        $nombreCompleto = $alumno->nombre_completo;

        // 3. VERIFICACIÓN (Assert)
        $this->assertEquals('Juan Perez Gomez', $nombreCompleto);
    }

    /**
     * Prueba que el nombre completo maneja apellidos nulos.
     * @return void
     */
    public function test_el_nombre_completo_maneja_apellido_materno_nulo(): void
    {
        // 1. PREPARACIÓN
        $alumno = new Alumno([
            'nombre' => 'Ana',
            'apellido_paterno' => 'Lopez',
            'apellido_materno' => null
        ]);

        // 2. ACCIÓN
        $nombreCompleto = $alumno->nombre_completo;


        $this->assertEquals('Ana Lopez', $nombreCompleto);
    }
}
