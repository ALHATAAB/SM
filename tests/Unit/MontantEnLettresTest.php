<?php

namespace Tests\Unit;

use App\Services\MontantEnLettres;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MontantEnLettresTest extends TestCase
{
    #[DataProvider('cas')]
    public function test_conversion(int $nombre, string $attendu): void
    {
        $this->assertSame($attendu, MontantEnLettres::convertir($nombre));
    }

    public static function cas(): array
    {
        return [
            [0, 'zéro'],
            [1, 'un'],
            [21, 'vingt et un'],
            [71, 'soixante et onze'],
            [80, 'quatre-vingts'],
            [81, 'quatre-vingt-un'],
            [99, 'quatre-vingt-dix-neuf'],
            [100, 'cent'],
            [200, 'deux cents'],
            [201, 'deux cent un'],
            [1000, 'mille'],
            [2000, 'deux mille'],
            [80000, 'quatre-vingt mille'],
            [200000, 'deux cent mille'],
            [75000, 'soixante-quinze mille'],
            [1250000, 'un million deux cent cinquante mille'],
            [2000000, 'deux millions'],
            [200000000, 'deux cents millions'],
            [2026, 'deux mille vingt-six'],
        ];
    }
}
