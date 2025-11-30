<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Grupo;
use App\Models\Carrera; // Necesarios para crear grupo manual
use App\Models\Cuatrimestre; // Necesarios para crear grupo manual
use Illuminate\Foundation\Testing\RefreshDatabase;

class GrupoUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function sugiere_nombre_incrementando_numero()
    {
        // CORRECCIÓN: Usamos un nombre que termine en número para que tu Regex funcione
        $grupo = new Grupo(['nombre' => 'ISC-2']);

        $sugerencia = $grupo->getNombreSugeridoPromocion();

        // Esperamos que 2 cambie a 3
        $this->assertEquals('ISC-3', $sugerencia);
    }

    /** @test */
    public function sugiere_nombre_con_promo_si_no_hay_numero()
    {
        $grupo = new Grupo(['nombre' => 'Grupo-Sin-Numero']);

        $sugerencia = $grupo->getNombreSugeridoPromocion();

        $this->assertEquals('Grupo-Sin-Numero-PROMO', $sugerencia);
    }

    /** @test */
    public function un_grupo_puede_ser_desactivado_unitariamente()
    {
        // CORRECCIÓN: Creamos dependencias para no usar Factory
        $carrera = Carrera::create(['nombre' => 'Sistemas', 'esta_activo' => 1]);
        $cuatri = Cuatrimestre::create(['nombre' => 'Q1', 'esta_activo' => 1, 'fecha_inicio' => now(), 'fecha_fin' => now()]);

        // Creamos el grupo manualmente
        $grupo = Grupo::create([
            'nombre' => 'G-Test',
            'carrera_id' => $carrera->id,
            'cuatrimestre_id' => $cuatri->id,
            'esta_activo' => true
        ]);

        // Simulamos lo que hace el controller
        $grupo->esta_activo = false;
        $grupo->save();

        $this->assertFalse((bool)$grupo->fresh()->esta_activo);
    }
}
