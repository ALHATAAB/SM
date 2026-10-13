<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Photo (chemin sur le disque « public ») de l'élève, de l'enseignant et de l'employé. */
    private array $tables = ['pre_inscription_models', 'enseignant_models', 'employer_models'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'photo')) {
                Schema::table($table, fn (Blueprint $t) => $t->string('photo')->nullable());
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'photo')) {
                Schema::table($table, fn (Blueprint $t) => $t->dropColumn('photo'));
            }
        }
    }
};
