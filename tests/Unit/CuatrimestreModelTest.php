<?php

 // JESÚS ALEJANDRO OROZCO SANDOVAL

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Cuatrimestre;
use Carbon\Carbon;

class CuatrimestreModelTest extends TestCase
{
    /**
     * Prueba que el cuatrimestre detecta que está vigente.
     * @return void
     */
    public function test_un_cuatrimestre_esta_vigente_hoy(): void
    {

        // Simulamos que "hoy" es 15 de Octubre
        Carbon::setTestNow(Carbon::create(2025, 10, 15));

        $cuatrimestre = new Cuatrimestre([
            'fecha_inicio' => '2025-09-01',
            'fecha_fin' => '2025-12-31'
        ]);

        // 2. ACCIÓN Y VERIFICACIÓN
        $this->assertTrue($cuatrimestre->estaVigenteHoy());

        // Limpiamos la simulación de tiempo
        Carbon::setTestNow();
    }

    /**
     * Prueba que el cuatrimestre detecta que NO está vigente.
     * @return void
     */
    public function test_un_cuatrimestre_ya_paso(): void
    {

        // Simulamos que "hoy" es 15 de Octubre
        Carbon::setTestNow(Carbon::create(2025, 10, 15));

        $cuatrimestre = new Cuatrimestre([
            'fecha_inicio' => '2025-01-01',
            'fecha_fin' => '2025-04-30'
        ]);

        // 2. ACCIÓN Y VERIFICACIÓN
        $this->assertFalse($cuatrimestre->estaVigenteHoy());

        // Limpiamos la simulación de tiempo
        Carbon::setTestNow();
    }
}
