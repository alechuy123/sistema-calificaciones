<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlumnoUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function un_alumno_puede_ser_desactivado()
    {
        // 1. Preparación: Creamos las dependencias manualmente para evitar error de Factory
        // Agregamos fechas al ciclo para evitar el error SQL 1364
        $carrera = Carrera::create(['nombre' => 'Sistemas', 'esta_activo' => 1]);
        $ciclo = CicloEscolar::create([
            'nombre' => '2024-2025',
            'esta_activo' => 1,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addYear()
        ]);

        $alumno = Alumno::create([
            'nombre' => 'Juan',
            'apellido_paterno' => 'Perez',
            'carrera_id' => $carrera->id,
            'ciclo_escolar_id' => $ciclo->id,
            'esta_activo' => true
        ]);

        // 2. Acción
        $alumno->desactivar();

        // 3. Verificación
        $this->assertFalse((bool)$alumno->esta_activo);
        $this->assertDatabaseHas('alumnos', [
            'id' => $alumno->id,
            'esta_activo' => 0
        ]);
    }

    /** @test */
    public function un_alumno_puede_ser_reactivado()
    {
        // 1. Preparación
        $carrera = Carrera::create(['nombre' => 'Sistemas', 'esta_activo' => 1]);
        $ciclo = CicloEscolar::create([
            'nombre' => '2024-2025',
            'esta_activo' => 1,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addYear()
        ]);

        $alumno = Alumno::create([
            'nombre' => 'Maria',
            'apellido_paterno' => 'Lopez',
            'carrera_id' => $carrera->id,
            'ciclo_escolar_id' => $ciclo->id,
            'esta_activo' => false
        ]);

        // 2. Acción
        $alumno->activar();

        // 3. Verificación
        $this->assertTrue((bool)$alumno->esta_activo);
    }

    /** @test */
    public function verifica_el_nombre_completo_correctamente()
    {
        $alumno = new Alumno([
            'nombre' => 'Juan',
            'apellido_paterno' => 'Perez',
            'apellido_materno' => 'Lopez'
        ]);

        $this->assertEquals('Juan Perez Lopez', $alumno->nombre_completo);
    }
}
