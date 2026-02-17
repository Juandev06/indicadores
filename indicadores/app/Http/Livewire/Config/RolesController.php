<?php

namespace App\Http\Livewire\Config;

use App\Models\Config\Area;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;
use App\Models\Status;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RolesController extends Component
{
    use WithPagination;

    public
        $permisoModulo, // nombre del permiso de módulo actual
        $usuarioAct, // usuario actual (con sesión activa)
        $areaUsuario, // área del usuario actual
        $name, $search, $selected_id, $status, $PageTitle, $ComponentName, $pagination;


    public function mount()
    {
        $this->permisoModulo = 'roles';
        $this->usuarioAct = Auth::user();
        $this->areaUsuario = Area::find($this->usuarioAct->id_area)->name;
        $this->PageTitle = 'Listado';
        $this->ComponentName = 'Perfiles';
        $this->selected_id = 0;
        $this->status = 'A';
        $this->pagination = env('PAGINATION');
    }

    public function render()
    {
        // validar el ingreso del usuario al módulo actual
        if (! $this->usuarioAct->can($this->permisoModulo)) {
            return abort('403');
        }
        //Obtiene la informacion de los roles
        if (strlen($this->search) > 0) {
            $this->resetPage();
            $data = Role::join('statuses as c', 'c.code', 'roles.status')
                ->select('roles.*', 'c.name as status')
                ->where('roles.name', 'like', '%' . $this->search . '%')
                ->orWhere('c.name', 'like', '%' . $this->search . '%')
                ->orderBy('roles.name')
                ->paginate($this->pagination);
        } else {
            $data = Role::join('statuses as c', 'c.code', 'roles.status')
                ->select('roles.*', 'c.name as status')
                ->orderBy('name')
                ->paginate($this->pagination);
        }

        return view('livewire.profile.profiles',  [
            'profiles' => $data,
            'statuses' =>  Status::orderBy('name')->get()
        ])
            ->extends('layouts.theme.app')
            ->section('content');
    }
    //Metodo para la paginacion personalizada 
    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    //Metodo para almacenar
    public function CreateProfile()
    {
        $rules = [
            'name' => 'required|unique:Roles|min:2',
        ];
        //Mnesajes personalizados
        $messages = [
            'name.required' => 'Debe ingresar un perfil',
            'name.unique' => 'Ya existe un perfil en la base de datos',
            'name.min' => 'El nombre debe conteneder al menos  2 carácteres',
        ];
        $this->validate($rules, $messages);
        $area = Role::create([
            'name' => $this->name,
            'status' => 'A'
        ]);
        $this->ResetUI();
        $this->emit('profile-added', 'Perfil agregado');
    }
    //Metodo al momento de cerrar el modal
    public function ResetUI()
    {
        $this->name = '';
        $this->search = '';
        $this->selected_id = 0;
        $this->resetValidation();
        $this->resetPage();
    }

    //Metodo que setea los datos del rol a editar 
    public function Edit(Role $profile)
    {
        $this->selected_id = $profile->id;
        $this->name = $profile->name;
        $this->status = $profile->status;
        $this->emit('show-modal', 'Editar perfil');
    }

    //Metodo que actualiza el perfil 
    public function UpdateProfile()
    {
        $rules = [
            'name' => "required|min:2|unique:roles,name,{$this->selected_id}",
            'status' => 'required'
        ];
        //Mnesajes personalizados
        $messages = [
            'name.required' => 'Debe ingresar un nombre',
            'name.unique' => 'Ya existe un registro en la base de datos',
            'name.min' => 'El nombre debe conteneder al menos  2 carácteres',
        ];
        $this->validate($rules, $messages);
        $profile = Role::find($this->selected_id);
        $profile->update([
            'name' => $this->name,
            'status' => $this->status
        ]);
        $profile->save();
        $this->ResetUI();
        $this->emit('profile-updated', 'Perfil actualizado');
    }

    protected $listeners = [
        'deleteRow' => 'Destroy'
    ];

    //Mètodo para eliminar
    public function Destroy($id)
    {
        //valida que no tenga usuarios y roles asociados
        $permissionCount = Role::find($id)->permissions->count();
        if ($permissionCount > 0) {
            $this->emit('profile-error', 'No se puede eliminar porque tiene permisos asociados!');
            return;
        }
        $user = User::where('id_rol', $id)->count();
        if ($user > 0) {
            $this->emit('profile-user', 'El perfil tiene usuarios asociados');
            return;
        }
        Role::find($id)->delete();
        $this->ResetUI();
        $this->emit('profile-deleted', 'Perfil eliminado');
    }
}
