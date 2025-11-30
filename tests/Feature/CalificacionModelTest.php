<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Calificacion;
use App\Models\Alumno;
use App\Models\Instrumento;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Models\Unidad;
use App\Models\Materia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CalificacionModelTest extends TestCase
{
    use RefreshDatabase; // Borra y crea la BD para cada prueba

    /**
     * Prueba 1: Verificar que se puede crear una calificación.
     * Esto prueba que tu array $fillable está bien configurado.
     */
    public function test_se_puede_crear_una_calificacion()
    {
        // 1. Crear dependencias 
        $alumno = $this->crearAlumno();
        $instrumento = $this->crearInstrumento();

        // 2. Crear la Calificación usando el Modelo
        $calificacion = Calificacion::create([
            'alumno_id' => $alumno->id,
            'instrumento_id' => $instrumento->id,
            'calificacion_obtenida' => 9.5
        ]);

        // 3. Verificaciones
        $this->assertNotNull($calificacion->id);
        $this->assertEquals(9.5, $calificacion->calificacion_obtenida); 
        
       
        $this->assertDatabaseHas('calificaciones', [
            'id' => $calificacion->id,
            'calificacion_obtenida' => 9.5
        ]);
    }

    /**
     * Prueba 2: Verificar la relación .
     */
    public function test_una_calificacion_pertenece_a_un_alumno()
    {
        // 1. Crear datos
        $alumno = $this->crearAlumno();
        $instrumento = $this->crearInstrumento();
        
        $calificacion = Calificacion::create([
            'alumno_id' => $alumno->id,
            'instrumento_id' => $instrumento->id,
            'calificacion_obtenida' => 8.0
        ]);

        // 2. Probar la relación 
        $this->assertInstanceOf(Alumno::class, $calificacion->alumno);
        $this->assertEquals($alumno->id, $calificacion->alumno->id);
    }

    /**
     * Prueba 3: Verificar la relación "BelongsTo Instrumento".
     */
    public function test_una_calificacion_pertenece_a_un_instrumento()
    {
        // 1. Crear datos
        $alumno = $this->crearAlumno();
        $instrumento = $this->crearInstrumento();
        
        $calificacion = Calificacion::create([
            'alumno_id' => $alumno->id,
            'instrumento_id' => $instrumento->id,
            'calificacion_obtenida' => 10.0
        ]);

        // 2. Probar la relación 
        $this->assertInstanceOf(Instrumento::class, $calificacion->instrumento);
        $this->assertEquals($instrumento->nombre, $calificacion->instrumento->nombre);
    }

    // --- FUNCIONES AUXILIARES (Para no repetir código) ---

    private function crearAlumno()
    {
        // Necesitamos Carrera y Ciclo para crear un Alumno (por las FKs)
        $carrera = Carrera::create(['nombre' => 'Sistemas', 'esta_activo' => 1]);
        $ciclo = CicloEscolar::create([
            'nombre' => '2025', 
            'fecha_inicio' => now(), 
            'fecha_fin' => now()->addYear(), 
            'esta_activo' => 1
        ]);

        return Alumno::create([
            'nombre' => 'Test Alumno',
            'apellido_paterno' => 'Perez',
            'matricula' => 'MAT-' . rand(1000, 9999),
            'carrera_id' => $carrera->id,
            'ciclo_escolar_id' => $ciclo->id,
            'esta_activo' => 1
        ]);
    }

    private function crearInstrumento()
    {
        // Necesitamos Materia y Unidad para crear un Instrumento
        $materia = Materia::create([
            'nombre' => 'Materia Test', 
            'objetivo' => 'Obj', 
            'esta_activo' => 1
        ]);
        
        $unidad = Unidad::create([
            'nombre' => 'Unidad 1', 
            'materia_id' => $materia->id
        ]);

        return Instrumento::create([
            'nombre' => 'Examen Parcial',
            'unidad_id' => $unidad->id,
            'porcentaje' => 50
        ]);
    }
}