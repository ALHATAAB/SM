<?php

use App\Models\Auth\RoleModel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('role_models', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->timestamps();
        });

        $role=[
            'nom'=>'Admin',
        ];

        RoleModel::create($role);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_models');
    }
};
