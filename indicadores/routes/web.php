<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Auth::routes();

Route::view('/', 'auth.login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);
Route::view('/register','auth.register');

Route::middleware(['auth'])->group(function () {
    // configuración
    Route::get('areas', App\Http\Livewire\Config\AreasController::class);
    Route::get('categories', App\Http\Livewire\Config\CategoriasController::class);
    Route::get('subcategories', App\Http\Livewire\Config\SubcategoriasController::class);
    Route::get('users', App\Http\Livewire\Config\UsuariosController::class);
    Route::get('profiles', App\Http\Livewire\Config\RolesController::class);
    Route::get('permissions', App\Http\Livewire\Config\PermisosController::class);
    // variables
    Route::get('variables', App\Http\Livewire\Variables\VariablesConfigController::class);
    Route::get('enablevariable', App\Http\Livewire\Variables\VariableHabilitarPeriodoController::class);
    Route::get('setvariable', App\Http\Livewire\Variables\VariableValoresController::class);
    // indicadores
    Route::get('indicators', App\Http\Livewire\Indicadores\IndicadoresController::class);
    // metas
    Route::get('goals', App\Http\Livewire\Metas\MetasController::class);
    // reportes
    Route::get('analysis', App\Http\Livewire\Reportes\AnalisisIndicadoresController::class);
    Route::get('dashboard/{ano?}', App\Http\Livewire\Reportes\DashboardController::class)->name('dashboard');
});
