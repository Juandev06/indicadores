<?php

namespace App\Http\Livewire\Config;

use App\Models\Config\Area;
use Livewire\Component;
use App\Models\Config\Categorias;
use App\Models\Indicadores\IndicadorCategorias;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class CategoriasController extends Component
{
    use WithPagination;

    public
        $permisoModulo, // nombre del permiso de módulo actual
        $permisoEditarModulo, // nombre del permiso para edicion del modulo
        $usuarioAct, // usuario actual (con sesión activa)
        $areaUsuario, // área del usuario actual
        $search,
        $selected_id,
        $PageTitle,
        $ComponentName,
        $pagination,
        $estado,
        $nombre;

    protected $listeners = [ 'deleteRow' => 'Destroy' ];

    public function mount()
    {
        $this->permisoModulo = 'categorias';
        $this->permisoEditarModulo = 'categorias_editar';
        $this->usuarioAct = Auth::user();
        $this->areaUsuario = Area::find($this->usuarioAct->id_area)->name;
        $this->PageTitle = 'Listado';
        $this->ComponentName = 'Categorias';
        $this->pagination = env('PAGINATION');
        $this->selected_id = 0;
        $this->estado = 'A';
        $this->nombre = '';
    }

    public function render()
    {
        // validar el ingreso del usuario al módulo actual
        if (! $this->usuarioAct->can($this->permisoModulo)) {
            return abort('403');
        }
        $strSearch = $this->search == '' ? false : ('%' . str_replace(' ', '%', $this->search) . '%');
        $categorias = Categorias::when($strSearch, function ($query, $strSearch) {
            return $query->where('nombre', 'like', $strSearch);
        })
            ->orderBy('nombre')
            ->paginate($this->pagination);

        return view('livewire.config.categorias.categorias', ['categorias' => $categorias])
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function ResetUI()
    {
        $this->nombre = '';
        $this->estado = 'A';
        $this->selected_id = 0;
        $this->resetValidation();
        $this->resetPage();
    }

    public function Store()
    {
        $rules = [
            'nombre' => 'required|unique:categorias|min:2',
        ];
        $messages = [
            'nombre.required' => 'Debe ingresar el nombre de la categoría',
            'nombre.unique' => 'Ya existe la categoría',
            'nombre.min' => 'El nombre debe conteneder al menos  2 carácteres',
        ];
        $this->validate($rules, $messages);

        $categoria = Categorias::create([
            'nombre' => $this->nombre,
            'estado' => 'A'
        ]);
        $categoria->save();
        $this->ResetUI();
        $this->emit('category-added', 'Categoría agregada');
    }

    public function Edit($id)
    {
        $categoria = Categorias::find($id);
        $this->nombre = $categoria->nombre;
        $this->selected_id = $categoria->id;
        $this->estado = $categoria->estado;
        $this->emit('show-modal', 'Registro activo para editar!!');
    }

    public function Update()
    {
        $rules = [
            'nombre' => 'required|min:2',
            'estado' => 'required'
        ];
        $messages = [
            'nombre.required' => 'Debe ingresar el nombre de la categoría',
            'nombre.min' => 'El nombre debe conteneder al menos  2 carácteres',
            'nombre.unique' => 'Ya existe la categoría',
        ];

        $this->validate($rules, $messages);
        $categoria = Categorias::find($this->selected_id);
        $categoria->update([
            'nombre' => $this->nombre,
            'estado' => $this->estado
        ]);
        $this->ResetUI();
        $this->emit('category-updated', 'Categoría actualizada');
    }

    public function Destroy(Categorias $categoria)
    {
        $indicador = IndicadorCategorias::where('id_categoria', $categoria->id)->count();
        if ($indicador > 0) {
            $this->emit('cat-indicators', 'La categoría tiene indicadores asociados.');
            return;
        }
        $categoria->delete();
        $this->ResetUI();
        $this->emit('category-deleted', 'Categoría Eliminada');
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }
}
