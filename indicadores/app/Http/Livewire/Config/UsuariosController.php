<?php

namespace App\Http\Livewire\Config;

use Illuminate\Validation\Rule;
use DB;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Config\Area;
use App\Models\Status;
use App\Models\Variables\Variables;
use App\Models\Aux\Periodos;
use App\Models\User;
use App\Models\Type;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\WithFileUploads;
use App\Models\Indicadores\Indicadores;
use Illuminate\Support\Facades\Auth;

class UsuariosController extends Component
{
    use WithPagination;
    use WithFileUploads;
    public 
        $permisoModulo, // nombre del permiso de módulo actual
        $usuarioAct, // usuario actual (con sesión activa)
        $areaUsuario, // área del usuario actual
        $ComponentName, $selected_id, $name, $phone, $email, $identification, $area,
        $status, $image, $password, $fileLoaded, $profile, $PageTitle, $address,
        $search, $lastName, $area_id, $confirm_password, $pagination;

    public function mount()
    {
        $this->permisoModulo = 'usuarios';
        $this->usuarioAct = Auth::user();
        $this->areaUsuario = Area::find($this->usuarioAct->id_area)->name;
        $this->ComponentName = 'Usuario';
        $this->PageTitle = "Listado";
        $this->selected_id = 0;
        $this->status = 'A';
        $this->profile = 'Elegir';
        $this->area = 'Elegir';
        $this->pagination = env('PAGINATION');
    }
    public function render()
    {
        // validar el ingreso del usuario al módulo actual
        if (! $this->usuarioAct->can($this->permisoModulo)) {
            return abort('403');
        }
        //Obtiene el listado de usuarios
        if (strlen($this->search) > 0) {
            $this->resetPage();
            $data = User::join('statuses as c', 'c.code', 'users.status')
                ->join('areas as d', 'd.id', 'users.id_area')
                ->join('roles as r', 'r.id', 'users.id_rol')
                ->where('users.name', 'like', '%' . $this->search . '%')
                ->orWhere('users.lastName', 'like', '%' . $this->search . '%')
                ->orWhere('users.lastName', 'like', '%' . $this->search . '%')
                ->orWhere('c.name', 'like', '%' . $this->search . '%')
                ->orWhere('users.address', 'like', '%' . $this->search . '%')
                ->orWhere('users.phone', 'like', '%' . $this->search . '%')
                ->orWhere('users.identification', 'like', '%' . $this->search . '%')
                ->orWhere('r.name', 'like', '%' . $this->search . '%')
                ->orWhere('d.name', 'like', '%' . $this->search . '%')
                ->select('users.*', 'c.name as status', 'd.id as area_id', 'd.name as area', 'r.name as profile')
                ->orderBy('name')
                ->paginate($this->pagination);
        } else {
            $data = User::join('statuses as c', 'c.code', 'users.status')
                ->join('areas as d', 'd.id', 'users.id_area')
                ->join('roles as r', 'r.id', 'users.id_rol')
                ->select('users.*', 'c.name as status', 'd.name as area', 'r.name as profile')
                ->orderBy('name')
                ->paginate($this->pagination);
        }
        return view('livewire.users.user', [
            'statuses' =>  Status::orderBy('name')->get(),
            'data' => $data,
            'profiles' => Role::where('status', 'A')->orderBy('name')->get(),
            'areas' => Area::where('status', 'A')->orderBy('name')->get(),
        ])->extends('layouts.theme.app')
            ->section('content');
    }

    //Metodo para la paginacion personalizada 
    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function ResetUI()
    {
        $this->phone = '';
        $this->email = '';
        $this->name = '';
        $this->lastName = '';
        $this->image = '';
        $this->password = '';
        $this->profile = '';
        $this->search = '';
        $this->selected_id = 0;
        $this->identification = '';
        $this->area_id = 0;
        $this->confirm_password = '';
        $this->address = '';
        $this->resetValidation();
        $this->resetPage();
    }

    //Setea los datos del registro a actualizar
    public function Edit(User $user)
    {
        $this->selected_id = $user->id;
        $this->name = $user->name;
        $this->lastName = $user->lastName;
        $this->status = $user->status;
        $this->phone = $user->phone;
        $this->email = $user->email;
        //$this->image = $user ->image;
        $this->profile = $user->id_rol;
        $this->area = $user->id_area;
        $this->address = $user->address;
        $this->identification = $user->identification;
        $this->area_id = $user->id_area;
        $this->emit('show-modal', 'Editar Usuario');
    }

    //Escuchar los eventos que llegan de javascript
    protected $listeners = [
        'deleteRow' => 'Destroy',
        'ResetUI' => 'ResetUI',
    ];

    //Metodo para almacenar
    public function Store()
    {
        if ($this->password != $this->confirm_password) {
            return back()->with('failPass', 'Las contraseñas no son iguales');
        }
        $rules = [
            'name' => 'required|min:4', //Regla de  validacion para nombre
            'lastName' => 'required|min:4', //Regla de  validacion para nombre
            'address' => 'required|min:4', //Regla de  validacion para nombre
            'email' => 'required|unique:users|email|min:4',
            'profile' => 'required|not_in:Elegir',
            'password' => 'required|min:4',
            'identification' => 'required|numeric',
            'area_id' => 'required|not_in:Elegir'
        ];
        //Mnesajes personalizados
        $messages = [
            'name.min' => 'El nombre debe conteneder al menos  2 carácteres',
            'lastName.min' => 'El apellido debe conteneder al menos  2 carácteres',
            'address.min' => 'La dirección debe conteneder al menos  2 carácteres',
            'name.required' => 'Debe ingresar un nombre',
            'lastName.required' => 'Debe ingresar el apellido',
            'address.required' => 'Debe ingresar uns direccion',
            'password.min' => 'Debe conteneder al menos  4 carácteres',
            'password.required' => 'Debe ingresar un tipo',
            'emal.unique' => 'Ya existe un tipo en la base de datos',
            'email.required' => 'El nombre debe conteneder al menos  4 carácteres',
            'email.min' => 'El nombre debe conteneder al menos  2 carácteres',
            'profile.not_in' => 'Seleccione el Perfil',
            'profile.required' => 'Seleccione el Perfil',
            'identification.required' => 'Debe ingresar un numero de identificación',
            'identification.numeric' => 'Debe ingresar solamente números',
            'area_id.required' => 'Debe seleccionar el área',
            'area_id.not_in' => 'Debe seleccionar el área'
        ];
        $this->validate($rules, $messages);
        $user = User::create([
            'name' => $this->name,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => bcrypt($this->password),
            'id_rol' => $this->profile,
            'id_area' => $this->area_id,
            'status' => 'A',
            'identification' => $this->identification,
            'address' => $this->address
        ]);
        $user->syncRoles($this->profile);
        if ($this->image) {
            $custonFileName = uniqid() . '_' . $this->image->extension(); //Aplica el nombre con la extension
            $this->image->storeAs('public/users/', $custonFileName);
            $user->image = $custonFileName;
        }
        $user->save();
        $this->ResetUI();
        $this->emit('user-added', 'Usuario agregado');
    }

    //Metodo que actualiza
    public function Update()
    {
        if ($this->password != $this->confirm_password) {
            return back()->with('failPass', 'Las contraseñas no son iguales');
        }
        $rules = [
            'name' => 'required|min:4', //Regla de  validacion para nombre
            'email' => "required|min:2|unique:users,email,{$this->selected_id}", //Regla de  validacion para nombre
            'status' => 'required',
            'profile' => 'required|not_in:Elegir',
            'identification' => 'required|numeric',
            'area' => 'required|not_in:Elegir',
            'profile' => 'required|not_in:Elegir',
            'area' => 'required|not_in:Elegir'
        ];
        //Mnesajes personalizados
        $messages = [
            'name.min' => 'El nombre debe conteneder al menos  2 carácteres',
            'name.required' => 'Debe ingresar un tipo',
            'emal.unique' => 'Ya existe un tipo en la base de datos',
            'email.required' => 'El nombre debe conteneder al menos  2 carácteres',
            'email.min' => 'El nombre debe conteneder al menos  2 carácteres',
            'status.required' => 'Seleccione el estado',
            'profile.not_in' => 'Seleccione el Perfil',
            'profile.required' => 'Seleccione el Perfil',
            'identification.required' => 'Debe ingresar un numero de identificación',
            'identification.numeric' => 'Debe ingresar solamente números',
            'address.min' => 'La dirección debe conteneder al menos  2 carácteres',
            'address.required' => 'Debe ingresar uns dirección',
            'area.required' => 'Debe seleccionar el área',
            'area.not_in' => 'Debe seleccionar el área'
        ];
        $this->validate($rules, $messages);
        $user = User::find($this->selected_id);
        if ($this->password) {
            $user->update([
                'name' => $this->name,
                'lastName' => $this->lastName,
                'address' => $this->address,
                'id_area' => $this->area,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => bcrypt($this->password),
                'profile_id' => $this->profile,
                'status' => $this->status
            ]);
        } else {
            $user->update([
                'name' => $this->name,
                'lastName' => $this->lastName,
                'address' => $this->address,
                'id_area' => $this->area,
                'email' => $this->email,
                'phone' => $this->phone,
                'profile_id' => $this->profile,
                'status' => $this->status
            ]);
        }
        $user->syncRoles($this->profile);
        if ($this->image) {
            $custonFileName = uniqid() . '_' . $this->image->extension(); //Aplica el nombre con la extension
            $imageTemp = $user->image;
            $this->image->storeAs('public/users/', $custonFileName);
            $user->image = $custonFileName;
            //Validacion que borra la imagen del disco.
            if ($imageTemp != null) {
                if (file_exists('storage/users/' . $imageTemp)) {
                    unlink('storage/users/' . $imageTemp);
                }
            }
        }
        $user->save();
        $this->ResetUI();
        $this->emit('user-updated', 'Usuario actualizado');
    }

    //Mètodo para eliminar
    public function Destroy(User $user)
    {
        //Valida que no tenga datos asociados.
        $indicators = Indicator::where('user_id', '=', $user->id)->get();
        if ($indicators->count() > 0) {
            $this->emit('user-indicators', 'El usuario tiene indicadores asociados'); //El emit se una notificacion normal.
            return;
        }
        $variables_user = Variable::where('user_id', '=',  $user->id)->get();
        if ($variables_user->count() > 0) {
            $this->emit('user-variables', 'El usuario tiene variables asociados'); //El emit se una notificacion normal.
            return;
        }
        $user->delete();
        $this->ResetUI();
        $this->emit('user-deleted', 'Usuario eliminado');
    }
}
