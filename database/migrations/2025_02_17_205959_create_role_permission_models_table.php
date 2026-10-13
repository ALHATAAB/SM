<?php

use Illuminate\Support\Facades\Schema;
use App\Models\Auth\RolePermissionModel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role_permission_models', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id')->nullable();
            $table->integer('permission_id')->nullable();
            $table->timestamps();
        });

        $role_permission=[
            'permission_id'=>1,
            'role_id'=>1,
        ];

        RolePermissionModel::create($role_permission);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permission_models');
    }
};
