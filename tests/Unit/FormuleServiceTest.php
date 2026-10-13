<?php

namespace Tests\Unit;

use App\Services\FormuleService;
use PHPUnit\Framework\TestCase;

class FormuleServiceTest extends TestCase
{
    protected function f(string $formule, array $vars = [], array $constantes = []): float
    {
        return (new FormuleService())->evaluer($formule, $vars, $constantes);
    }

    public function test_formules_de_base(): void
    {
        $v = ['base' => 200000, 'taux' => 5, 'nombre' => 3];
        $this->assertSame(600000.0, $this->f('base*nombre', $v));
        $this->assertSame(10000.0, $this->f('base*taux', $v));            // taux 5 % => 0.05
        $this->assertSame(30000.0, $this->f('base*taux*nombre', $v));
        $this->assertSame(200003.0, $this->f('base+nombre', $v));
        $this->assertSame(-1.0, $this->f('1-2'));
        $this->assertSame(7.0, $this->f('1+2*3'));
        $this->assertSame(9.0, $this->f('(1+2)*3'));
    }

    public function test_constantes_et_fonctions(): void
    {
        $c = ['PLAFOND' => 500000.0, 'SEUIL' => 100000.0, 'TR_MAX' => 9.0];
        $this->assertSame(25000.0, $this->f('min(base;PLAFOND)*taux', ['base' => 800000, 'taux' => 5], $c));
        $this->assertSame(10000.0, $this->f('si(base>SEUIL;base*taux;0)', ['base' => 200000, 'taux' => 5], $c));
        $this->assertSame(0.0, $this->f('si(base>SEUIL;base*taux;0)', ['base' => 50000, 'taux' => 5], $c));
        // branche non choisie jamais calculée : pas de division par zéro
        $this->assertSame(0.0, $this->f('si(nombre>0;base/nombre;0)', ['base' => 10, 'nombre' => 0]));
        $this->assertSame(9.0, $this->f('TR_MAX', [], $c));
    }

    public function test_validation(): void
    {
        $s = new FormuleService();
        $this->assertNull($s->valider('base*taux*nombre', []));
        $this->assertNotNull($s->valider('base*', []));
        $this->assertNotNull($s->valider('base*INCONNUE', []));
        $this->assertNotNull($s->valider('system("ls")', []));
        $this->assertNotNull($s->valider('base; 1', []));
        $this->assertNotNull($s->valider('', []));
    }

    public function test_prime_anciennete(): void
    {
        $c = ['ANC_SEUIL' => 2.0, 'ANC_TAUX_AN' => 1.0, 'ANC_PLAFOND' => 25.0];
        $formule = 'si(anciennete>=ANC_SEUIL;base*min(anciennete*ANC_TAUX_AN;ANC_PLAFOND)/100;0)';

        $this->assertSame(0.0, $this->f($formule, ['base' => 200000, 'anciennete' => 1], $c));      // sous le seuil
        $this->assertSame(10000.0, $this->f($formule, ['base' => 200000, 'anciennete' => 5], $c));  // 5 % de 200 000
        $this->assertSame(50000.0, $this->f($formule, ['base' => 200000, 'anciennete' => 40], $c)); // plafonnée à 25 %
        $this->assertNull((new FormuleService())->valider($formule, $c));
    }

    public function test_taux_patronal_et_pas_de_nombre_en_dur(): void
    {
        $c = ['CNSS_TAUX_PATRONAL' => 16.4, 'ANPE_TAUX_PATRONAL' => 1.0, 'TAUX_CHARGE_2' => 10.0];
        $this->assertSame(82000.0, $this->f('base*CNSS_TAUX_PATRONAL/100', ['base' => 500000], $c));

        $s = new FormuleService();
        $this->assertNull($s->valider('base*CNSS_TAUX_PATRONAL/100', $c, true));
        $this->assertNotNull($s->valider('base*16.4/100', $c, true));   // nombre en dur refusé
        $this->assertNull($s->valider('base*16.4/100', $c, false));     // accepté hors mode strict
        $this->assertSame(5000.0, $this->f('base*taux_charge/100', ['base' => 50000, 'taux_charge' => 10]));
    }
}
