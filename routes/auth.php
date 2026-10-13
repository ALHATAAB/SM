<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Users;
use App\Livewire\Auth\RolePermission;
use App\Livewire\Auth\RolePermissionAdd;
use App\Livewire\Auth\RolePermissionEdit;
use App\Http\Controllers\Auth\ControllerLogout;
use App\Livewire\Auth\ProfilUser;

Route::Get('/', Login::class)->name('login');

Route::middleware(['middleware' => 'admin'])->controller(ControllerLogout::class)->group(function(){
    Route::get('/logout', 'logout')->name('logout');
});
Route::group(['middleware' => 'admin'], function(){
Route::get('users',Users::class);
Route::get('profile/{id}',ProfilUser::class);
Route::get('role/permission',RolePermission::class);
Route::get('role/permission/add',RolePermissionAdd::class);
Route::get('role/permission/edit/{id}',RolePermissionEdit::class);

});
