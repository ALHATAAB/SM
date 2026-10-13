<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Barème de l'Impôt sur les Traitements et Salaires (ITS) du Niger, tranches mensuelles.
     * Source : Code Général des Impôts du Niger, art. 65 (charges de famille) et art. 66 (tranches),
     * et barème ITS publié par la DGI. Le barème mensuel est reconduit pour 2026 (art. 150 nouveau,
     * Ordonnance n°2025-44).
     *
     * Chaque tranche est imposée à son taux sur la seule part du revenu comprise dans la tranche.
     * La dernière tranche (« au-delà de 1 000 000 ») a un maximum symbolique de 99 999 999.
     *
     * Les données ne sont insérées que si les tables sont vides, pour ne jamais écraser ni
     * dupliquer ce qui a déjà été saisi dans Paramètre paie RH.
     */
    private const TRANCHES = [
        [0,       25000,    1],
        [25000,   50000,    2],
        [50000,   100000,   6],
        [100000,  150000,   13],
        [150000,  300000,   25],
        [300000,  400000,   30],
        [400000,  700000,   32],
        [700000,  1000000,  34],
        [1000000, 99999999, 35],
    ];

    /** nombre de personnes à charge => abattement en % (art. 65 du CGI). */
    private const CHARGES = [0 => 0, 1 => 5, 2 => 10, 3 => 12, 4 => 13, 5 => 14, 6 => 15, 7 => 30];

    public function up(): void
    {
        $now = now();

        if (DB::table('taux_tranche_fiscale_models')->count() === 0) {
            DB::table('taux_tranche_fiscale_models')->insert(array_map(fn ($t) => [
                'min' => $t[0], 'max' => $t[1], 'taux_fiscale' => $t[2],
                'created_at' => $now, 'updated_at' => $now,
            ], self::TRANCHES));
        }

        if (DB::table('taux_charge_familliale_models')->count() === 0) {
            $rows = [];
            foreach (self::CHARGES as $nombre => $taux) {
                $rows[] = ['nombre_de_charge' => $nombre, 'taux_charge' => $taux, 'created_at' => $now, 'updated_at' => $now];
            }
            DB::table('taux_charge_familliale_models')->insert($rows);
        }
    }

    public function down(): void
    {
        foreach (self::TRANCHES as $t) {
            DB::table('taux_tranche_fiscale_models')
                ->where('min', $t[0])->where('max', $t[1])->where('taux_fiscale', $t[2])->delete();
        }
        foreach (self::CHARGES as $nombre => $taux) {
            DB::table('taux_charge_familliale_models')
                ->where('nombre_de_charge', $nombre)->where('taux_charge', $taux)->delete();
        }
    }
};
