<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Carrera;
use App\Models\Cuatrimestre;
use App\Models\CicloEscolar;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $carrera;
    protected $cuatrimestre;
    protected $ciclo;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Autenticación (Creamos un usuario y nos logueamos)
        $this->user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('12345678')
        ]);
        $this->actingAs($this->user);

        // 2. Datos Base (Necesarios para crear Alumnos y Grupos sin errores)
        $this->carrera = Carrera::create(['nombre' => 'Sistemas', 'esta_activo' => 1]);

        $this->cuatrimestre = Cuatrimestre::create([
            'nombre' => 'Sep-Dic 2024',
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addMonths(4),
            'esta_activo' => 1
        ]);

        $this->ciclo = CicloEscolar::create([
            'nombre' => '2024-2025',
            'esta_activo' => 1,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addYear()
        ]);
    }

    /** @test */
    public function el_dashboard_muestra_las_estadisticas_correctas()
    {
        // --- PREPARACIÓN (ARRANGE) ---

        // 1. Crear 3 Alumnos ACTIVOS
        Alumno::create(['nombre' => 'A1', 'apellido_paterno' => 'P', 'carrera_id' => $this->carrera->id, 'ciclo_escolar_id' => $this->ciclo->id, 'esta_activo' => 1]);
        Alumno::create(['nombre' => 'A2', 'apellido_paterno' => 'P', 'carrera_id' => $this->carrera->id, 'ciclo_escolar_id' => $this->ciclo->id, 'esta_activo' => 1]);
        Alumno::create(['nombre' => 'A3', 'apellido_paterno' => 'P', 'carrera_id' => $this->carrera->id, 'ciclo_escolar_id' => $this->ciclo->id, 'esta_activo' => 1]);

        // 2. Crear 1 Alumno INACTIVO (Este NO debería contarse)
        Alumno::create(['nombre' => 'Inactivo', 'apellido_paterno' => 'P', 'carrera_id' => $this->carrera->id, 'ciclo_escolar_id' => $this->ciclo->id, 'esta_activo' => 0]);

        // 3. Crear 2 Grupos ACTIVOS
        Grupo::create(['nombre' => 'G1', 'carrera_id' => $this->carrera->id, 'cuatrimestre_id' => $this->cuatrimestre->id, 'esta_activo' => 1]);
        Grupo::create(['nombre' => 'G2', 'carrera_id' => $this->carrera->id, 'cuatrimestre_id' => $this->cuatrimestre->id, 'esta_activo' => 1]);

        // --- ACCIÓN (ACT) ---
        // Visitamos la ruta del controlador DashboardController@index
        // Usamos 'action' para que funcione sin importar si tu ruta es '/' o '/dashboard'
        $response = $this->get(action([\App\Http\Controllers\DashboardController::class, 'index']));

        // --- VERIFICACIÓN (ASSERT) ---

        $response->assertStatus(200);

        // Verificamos que la vista reciba los números exactos
        // Debería haber 3 alumnos activos (no 4)
        $response->assertViewHas('alumnosActivos', 3);

        // Debería haber 2 grupos activos
        $response->assertViewHas('gruposActivos', 2);

        // Verificamos que se muestre el nombre del cuatrimestre en el HTML
        $response->assertSee('Sep-Dic 2024');
    }

    /** @test */
    public function el_dashboard_tiene_botones_de_acceso_directo_que_funcionan()
    {
        // --- ACCIÓN ---
        $response = $this->get(action([\App\Http\Controllers\DashboardController::class, 'index']));

        // --- VERIFICACIÓN ---

        // 1. Verificamos que el texto de los botones existe
        $response->assertSee('Registro de Alumnos');
        $response->assertSee('Gestión de Grupos');

        // 2. Verificamos que el ENLACE (href) correcto existe en el HTML
        // Esto confirma que el botón te lleva a donde debe
        $response->assertSee(route('alumnos.index'));
        $response->assertSee(route('grupos.index'));

        // Opcional: Verificar catálogos
        $response->assertSee(route('carreras.index'));
    }
}
