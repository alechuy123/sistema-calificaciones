<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Carrera;
use App\Models\Cuatrimestre;
use App\Models\User;

class GrupoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $carrera;
    protected $cuatrimestre;
    protected $materia1;
    protected $materia2;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password')
        ]);
        $this->actingAs($this->user);

        $this->carrera = Carrera::create(['nombre' => 'Mecatrónica', 'esta_activo' => 1]);

        $this->cuatrimestre = Cuatrimestre::create([
            'nombre' => 'Sep-Dic 2024',
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addMonths(4),
            'esta_activo' => 1
        ]);

        $this->materia1 = Materia::create(['nombre' => 'Cálculo', 'esta_activo' => 1]);
        $this->materia2 = Materia::create(['nombre' => 'Física', 'esta_activo' => 1]);

        $this->carrera->materias()->attach([$this->materia1->id, $this->materia2->id]);
    }

    /** @test */
    public function ajax_carga_materias_de_la_carrera_correctamente()
    {
        $response = $this->get(action([\App\Http\Controllers\GrupoController::class, 'getMateriasPorCarrera'], $this->carrera));
        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    /** @test */
    public function puede_registrar_grupo_con_materias()
    {
        $response = $this->post(route('grupos.store'), [
            'nombre' => 'Grupo-Test-1',
            'carrera_id' => $this->carrera->id,
            'cuatrimestre_id' => $this->cuatrimestre->id,
            'materias' => [$this->materia1->id, $this->materia2->id],
            'esta_activo' => 1
        ]);

        $response->assertRedirect(route('grupos.index'));

        $grupo = Grupo::where('nombre', 'Grupo-Test-1')->first();
        $this->assertNotNull($grupo);

        $this->assertDatabaseHas('grupo_materia', [
            'grupo_id' => $grupo->id,
            'materia_id' => $this->materia1->id
        ]);
    }

    /** @test */
    public function formulario_edicion_carga_materias_seleccionadas()
    {
        $grupo = Grupo::create([
            'nombre' => 'Grupo-Edit',
            'carrera_id' => $this->carrera->id,
            'cuatrimestre_id' => $this->cuatrimestre->id,
            'esta_activo' => 1
        ]);
        $grupo->materias()->attach($this->materia1->id);

        $response = $this->get(route('grupos.edit', $grupo));

        $response->assertStatus(200);
        $response->assertViewHas('materias_actuales_ids');

        $materiasIds = $response->viewData('materias_actuales_ids');
        $this->assertContains($this->materia1->id, $materiasIds);
    }

    /** @test */
    public function puede_promover_grupo_al_siguiente_cuatrimestre()
    {
        $grupoOriginal = Grupo::create([
            'nombre' => 'Mec-1A',
            'carrera_id' => $this->carrera->id,
            'cuatrimestre_id' => $this->cuatrimestre->id,
            'esta_activo' => 1
        ]);

        $cuatriFuturo = Cuatrimestre::create([
            'nombre' => 'Ene-Abr 2025',
            'fecha_inicio' => now()->addMonths(5),
            'fecha_fin' => now()->addMonths(9),
            'esta_activo' => 1
        ]);

        $response = $this->post(action([\App\Http\Controllers\GrupoController::class, 'promover'], $grupoOriginal), [
            'nombre' => 'Mec-2A',
            'cuatrimestre_id' => $cuatriFuturo->id
        ]);

        $response->assertRedirect(route('grupos.index'));

        $this->assertEquals(0, $grupoOriginal->fresh()->esta_activo);

        $grupoNuevo = Grupo::where('nombre', 'Mec-2A')->first();
        $this->assertNotNull($grupoNuevo);
        $this->assertEquals($grupoOriginal->id, $grupoNuevo->grupo_anterior_id);
    }

    /** @test */
    public function historial_muestra_grupo_anterior_y_siguiente()
    {
        $grupo1 = Grupo::create(['nombre' => 'G1', 'carrera_id' => $this->carrera->id, 'cuatrimestre_id' => $this->cuatrimestre->id, 'esta_activo' => 0]);

        $grupo2 = Grupo::create([
            'nombre' => 'G2',
            'carrera_id' => $this->carrera->id,
            'cuatrimestre_id' => $this->cuatrimestre->id,
            'esta_activo' => 1,
            'grupo_anterior_id' => $grupo1->id
        ]);

        $grupo3 = Grupo::create([
            'nombre' => 'G3',
            'carrera_id' => $this->carrera->id,
            'cuatrimestre_id' => $this->cuatrimestre->id,
            'esta_activo' => 1,
            'grupo_anterior_id' => $grupo2->id
        ]);

        $response = $this->get(route('grupos.show', $grupo2->id));

        $response->assertStatus(200);

        $grupoEnVista = $response->viewData('grupo');
        $this->assertEquals($grupo1->id, $grupoEnVista->grupoAnterior->id);
        $this->assertEquals($grupo3->id, $grupoEnVista->grupoSiguiente->id);
    }

    /** @test */
    public function puede_desactivar_y_reactivar_grupo()
    {
        $grupo = Grupo::create([
            'nombre' => 'Grupo-Activo',
            'carrera_id' => $this->carrera->id,
            'cuatrimestre_id' => $this->cuatrimestre->id,
            'esta_activo' => 1
        ]);

        // 1. Desactivar
        $this->delete(route('grupos.destroy', $grupo));
        $this->assertEquals(0, $grupo->fresh()->esta_activo);

        // 2. Reactivar
        // CORRECCIÓN: Enviamos materias existentes porque la validación 'required' lo exige
        $this->put(route('grupos.update', $grupo), [
            'nombre' => 'Grupo-Activo',
            'carrera_id' => $this->carrera->id,
            'cuatrimestre_id' => $this->cuatrimestre->id,
            'materias' => [$this->materia1->id], // <--- ESTO ES LA CLAVE PARA ARREGLARLO
            'esta_activo' => 1
        ]);

        $this->assertEquals(1, $grupo->fresh()->esta_activo);
    }
}
