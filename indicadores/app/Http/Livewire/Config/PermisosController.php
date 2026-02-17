<?php

namespace App\Http\Livewire\Config;

use App\Models\Config\Area;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class PermisosController extends Component
{
    public
        $permisoModulo, // nombre del permiso de módulo actual
        $usuarioAct, // usuario actual (con sesión activa)
        $areaUsuario, // área del usuario actual
        $rolesPermisos, // array de roles y permisos
        $rolSel, // rol seleccionado para filtrar listado
        $rolesDB, // listado de roles desde la base de datos
        $search,
        $pageTitle;

    public function mount()
    {
        $this->permisoModulo = 'permisos';
        $this->usuarioAct = Auth::user();
        $this->areaUsuario = Area::find($this->usuarioAct->id_area)->name;
        $this->pageTitle = 'Administrar Permisos por Rol';
        $this->rolesPermisos = [];
        $this->rolSel = 0;
        $this->rolesDB = Role::orderBy('name')->get();
    }
    public function render()
    {
        // validar el ingreso del usuario al módulo actual
        if (!$this->usuarioAct->can($this->permisoModulo)) {
            return abort('403');
        }
        $strSearch = $this->search == '' ? false : ('%' . str_replace(' ', '%', $this->search) . '%');
        $permisos = Permission::when($strSearch, function ($query, $strSearch) {
            return $query->where('name', 'like', $strSearch);
        })->orderBy('name')
            ->get();
        $rolSeleccionado = $this->rolSel == 0 ? false : $this->rolSel;
        $roles = Role::when($rolSeleccionado, function ($query, $rolSeleccionado) {
            return $query->where('id', $rolSeleccionado);
        })->orderBy('name')
            ->get();
        $permisosRoles = DB::table('role_has_permissions')->get();
        foreach ($roles as $rol) {
            foreach ($permisos as $permiso) {
                $tienePermiso = $permisosRoles->where('role_id', $rol->id)->where('permission_id', $permiso->id)->first();
                $this->rolesPermisos['r' . $rol->id . 'p' . $permiso->id] = is_null($tienePermiso) ? 0 : 1;
            }
        }
        return view('livewire.config.permisos.permisos', [
            'permisos' => $permisos,
            'roles' => $roles,
            'permisosRoles' => $permisosRoles,
        ])
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function UpdatePermiso($idRol, $idPermiso)
    {
        $rol = Role::find($idRol);
        $permisoNombre = Permission::find($idPermiso)->name;
        if ($rol->hasPermissionTo($permisoNombre)) {
            $rol->revokePermissionTo($permisoNombre);
        } else {
            $rol->givePermissionTo($permisoNombre);
        }
    }
}
