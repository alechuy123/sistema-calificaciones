<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Grupo;

class GrupoModelTest extends TestCase
{
    /**
     * Prueba que el nombre sugerido incrementa el número.
     * @return void
     */
    public function test_sugiere_nombre_incrementando_numero(): void
    {
        // 1. PREPARACIÓN
        $grupo = new Grupo(['nombre' => 'TIC-A10']);

        // 2. ACCIÓN
        $sugerencia = $grupo->getNombreSugeridoPromocion();

        // 3. VERIFICACIÓN
        $this->assertEquals('TIC-A11', $sugerencia);
    }

    /**
     * Prueba que el nombre sugerido añade -PROMO si no hay número.
     * @return void
     */
    public function test_sugiere_nombre_con_promo_si_no_hay_numero(): void
    {
        // 1. PREPARACIÓN
        $grupo = new Grupo(['nombre' => 'Grupo-Letras']);

        // 2. ACCIÓN
        $sugerencia = $grupo->getNombreSugeridoPromocion();

        // 3. VERIFICACIÓN
        $this->assertEquals('Grupo-Letras-PROMO', $sugerencia);
    }
}
