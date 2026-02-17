<?php

namespace App\Http\Livewire\Config;

use Livewire\Component;
use App\Models\Config\Area;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class AreasController extends Component
{
    use WithPagination;
    public
        $permisoModulo, // nombre del permiso de módulo actual
        $permisoEditarModulo, // nombre del permiso para edicion del modulo
        $usuarioAct, // usuario actual (con sesión activa)
        $areaUsuario, // área del usuario actual
        $name, 
        $search, 
        $selected_id, 
        $status, 
        $PageTitle, 
        $pagination, 
        $ComponentName;

    protected $listeners = [
        'deleteRow' => 'Destroy'
    ];

    public function mount()
    {
        $this->permisoModulo = 'areas';
        $this->permisoEditarModulo = 'areas_editar';
        $this->usuarioAct = Auth::user();
        $this->areaUsuario = Area::find($this->usuarioAct->id_area)->name;
        $this->PageTitle = 'Listado';
        $this->ComponentName = 'Areas';
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
        $strSearch = $this->search == '' ? false : ('%' . str_replace(' ', '%', $this->search) . '%');
        $areas = $areas = Area::when($strSearch, function ($query, $strSearch) {
            return $query->where('name', 'like', $strSearch);
        })
            ->orderBy('name')
            ->paginate($this->pagination);

        return view('livewire.config.area.areas', ['areas' => $areas])
            ->extends('layouts.theme.app')
            ->section('content');
    }

    //Metodo para editar
    public function Edit($id)
    {
        $record = Area::find($id, ['id', 'name', 'status']); // Devuelve los valos de las columnas especificadas en el array
        $this->name = $record->name;
        $this->selected_id = $record->id;
        $this->status = $record->status;
        //mostrar modal
        $this->emit('show-modal', 'Registro activo para editar!!');
    }

    //Metodo para insertar
    public function Store()
    {
        $rules = [
            'name' => 'required|unique:areas|min:2',
        ];
        //Mnesajes personalizados
        $messages = [
            'name.required' => 'Debe ingresar el nombre del área',
            'name.unique' => 'Ya existe el área',
            'name.min' => 'El nombre debe conteneder al menos  2 carácteres',
        ];
        $this->validate($rules, $messages);

        $area = Area::create([
            'name' => $this->name,
            'status' => 'A',
        ]);

        $area->save();
        $this->ResetUI();
        $this->emit('area-added', 'Area agregada');
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

    //Metodo para atualizar areas
    public function Update()
    {
        $rules = [
            'name' => "required|min:2|unique:areas,name,{$this->selected_id}", //Regla de  validacion para nombre
            'status' => 'required'
        ];
        $messages = [
            'name.required' => 'Debe ingresar el nombre del área',
            'name.min' => 'El nombre debe conteneder al menos  2 carácteres',
            'name.unique' => 'Ya existe el área',
            'status.required' => 'Debe seleccionar el estado'
        ];

        $this->validate($rules, $messages);
        $area = Area::find($this->selected_id);
        $area->update([
            'name' => $this->name,
            'status' => $this->status
        ]);
        $area->save();
        $this->ResetUI();
        $this->emit('area-updated', 'Area actualizada');
    }

    public function Destroy(Area $area)
    {
        // se valida si tiene datos asociados dependientes en usuarios
        $user = User::where('id_area', $area->id)->count();
        if ($user > 0) {
            $this->emit('area-users', 'El área tiene usuarios asociados');
            return;
        }
        $area->delete();
        $this->ResetUI();
        $this->emit('area-deleted', 'Area eliminada');
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }
}
