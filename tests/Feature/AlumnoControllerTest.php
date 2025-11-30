<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Models\Grupo;
use App\Models\Cuatrimestre;
use App\Models\User; // <--- IMPORTANTE: Importamos el modelo User

class AlumnoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $carrera;
    protected $ciclo;
    protected $grupo;
    protected $cuatrimestre;

    protected function setUp(): void
    {
        parent::setUp();

        // --- AUTENTICACIÓN (SOLUCIÓN AL ERROR 302) ---
        // Creamos un usuario manual para no depender de Factories
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'), // Encriptamos pass
        ]);

        // Le decimos a Laravel: "En esta prueba, actúa como este usuario"
        $this->actingAs($user);
        // ---------------------------------------------

        // 1. Crear Carrera
        $this->carrera = Carrera::create(['nombre' => 'Sistemas', 'esta_activo' => 1]);

        // 2. Crear Ciclo Escolar
        $this->ciclo = CicloEscolar::create([
            'nombre' => '2023-2024',
            'esta_activo' => 1,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addYear()
        ]);

        // 3. Crear Cuatrimestre
        $this->cuatrimestre = Cuatrimestre::create([
            'nombre' => '1er Cuatrimestre',
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addMonths(4)
        ]);

        // 4. Crear Grupo
        $this->grupo = Grupo::create([
            'nombre' => 'Grupo A',
            'carrera_id' => $this->carrera->id,
            'cuatrimestre_id' => $this->cuatrimestre->id,
            'esta_activo' => 1
        ]);
    }

    /** @test */
    public function puede_guardar_alumno_nuevo_y_asignar_grupo()
    {
        $datosFormulario = [
            'nombre' => 'Pedro',
            'apellido_paterno' => 'Picapiedra',
            'matricula' => '12345',
            'carrera_id' => $this->carrera->id,
            'ciclo_escolar_id' => $this->ciclo->id,
            'grupo_id' => $this->grupo->id,
            'esta_activo' => 1
        ];

        $response = $this->post(route('alumnos.store'), $datosFormulario);

        // Si esto falla ahora, revisaremos si redirige a otro lado, pero ya no debe ir al login
        $response->assertRedirect(route('alumnos.index'));

        $this->assertDatabaseHas('alumnos', [
            'matricula' => '12345',
            'nombre' => 'Pedro'
        ]);

        $alumnoCreado = Alumno::where('matricula', '12345')->first();

        $this->assertDatabaseHas('alumno_grupo', [
            'alumno_id' => $alumnoCreado->id,
            'grupo_id' => $this->grupo->id
        ]);
    }

    /** @test */
    public function formulario_edicion_carga_datos_y_grupo()
    {
        $alumno = Alumno::create([
            'nombre' => 'Maria',
            'apellido_paterno' => 'Lopez',
            'carrera_id' => $this->carrera->id,
            'ciclo_escolar_id' => $this->ciclo->id,
            'esta_activo' => 1
        ]);
        $alumno->grupos()->attach($this->grupo->id);

        $response = $this->get(route('alumnos.edit', $alumno));

        $response->assertStatus(200);
        $response->assertViewHas('grupo_actual_id', $this->grupo->id);
    }

    /** @test */
    public function listado_muestra_alumnos_y_status_ok()
    {
        $alumno = Alumno::create([
            'nombre' => 'Luis',
            'apellido_paterno' => 'Test',
            'carrera_id' => $this->carrera->id,
            'ciclo_escolar_id' => $this->ciclo->id
        ]);

        $response = $this->get(route('alumnos.index'));

        $response->assertStatus(200);
        $response->assertSee('Luis');
    }
}
