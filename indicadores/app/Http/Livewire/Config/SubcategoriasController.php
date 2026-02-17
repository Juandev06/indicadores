<?php

namespace App\Http\Livewire\Config;

use App\Models\Config\Area;
use Livewire\Component;
use App\Models\Config\Categorias;
use App\Models\Config\Subcategorias;
use Livewire\WithPagination;
use App\Models\Indicadores\IndicadorCategorias;
use Illuminate\Support\Facades\Auth;

class SubcategoriasController extends Component
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
        $nombre,
        $estado,
        $id_categoria;

    protected $listeners = ['deleteRow' => 'Destroy'];

    public function mount()
    {
        $this->permisoModulo = 'subcategorias';
        $this->permisoEditarModulo = 'subcategorias_editar';
        $this->usuarioAct = Auth::user();
        $this->areaUsuario = Area::find($this->usuarioAct->id_area)->name;
        $this->PageTitle = 'Listado';
        $this->ComponentName = 'Subcategorias';
        $this->selected_id = 0;
        $this->estado = 'A';
        $this->pagination = env('PAGINATION');
    }

    public function render()
    {
        // validar el ingreso del usuario al módulo actual
        if (! $this->usuarioAct->can($this->permisoModulo)) {
            return abort('403');
        }
        $strSearch = $this->search == '' ? false : ('%' . str_replace(' ', '%', $this->search) . '%');

        $subcategorias = Subcategorias::join('categorias', 'categorias.id', 'subcategorias.id_categoria')
            ->select('subcategorias.*', 'categorias.nombre as categoria')
            ->when($strSearch, function ($query, $strSearch) {
                return $query->whereRaw('concat(subcategorias.nombre, categorias.nombre) like ?', [$strSearch]);
            })
            ->orderBy('categorias.nombre')
            ->orderBy('subcategorias.nombre')
            ->paginate($this->pagination);

        return view('livewire.config.subcategorias.subcategorias', [
            'subcategorias' => $subcategorias,
            'categorias' => Categorias::orderBy('nombre')->get(),
        ])->extends('layouts.theme.app')
            ->section('content');
    }

    public function ResetUI()
    {
        $this->nombre = '';
        $this->estado = 'A';
        $this->id_categoria = 0;
        $this->selected_id = 0;
        $this->resetValidation();
        $this->resetPage();
    }

    public function Store()
    {
        $rules = [
            'nombre' => 'required|unique:subcategorias|min:2',
            'id_categoria' => 'required|not_in:0'
        ];
        //Mnesajes personalizados
        $messages = [
            'nombre.required' => 'Debe ingresar el nombre de la subcategoría',
            'nombre.unique' => 'Ya existe la subcategoría',
            'nombre.min' => 'El nombre debe conteneder al menos  2 carácteres',
            'id_categoria.required' => 'Seleccione la categoría',
            'id_categoria.not_in' => 'Seleccione la categoría',
        ];
        $this->validate($rules, $messages);

        $categoria = Subcategorias::create([
            'nombre' => $this->nombre,
            'estado' => 'A',
            'id_categoria' => $this->id_categoria
        ]);
        $this->ResetUI();
        $this->emit('subcategory-added', 'Subcategoría agregada');
    }

    public function Edit($id)
    {
        $subcategoria = Subcategorias::find($id);
        $this->nombre = $subcategoria->nombre;
        $this->selected_id = $subcategoria->id;
        $this->estado = $subcategoria->estado;
        $this->id_categoria = $subcategoria->id_categoria;
        $this->emit('show-modal', 'Registro activo para editar!!');
    }

    public function Update()
    {
        $rules = [
            'nombre' => 'required|min:2',
            'estado' => 'required',
            'id_categoria' => 'required|not_in:0'
        ];
        $messages = [
            'nombre.required' => 'Debe ingresar el nombre de la subcategoría',
            'nombre.min' => 'El nombre debe conteneder al menos  2 carácteres',
            'nombre.unique' => 'Ya existe la subcategoría',
            'id_categoria.not_in' => 'Seleccione la categoría'
        ];

        $this->validate($rules, $messages);
        $subcategoria = Subcategorias::find($this->selected_id);
        $subcategoria->update([
            'nombre' => $this->nombre,
            'estado' => $this->estado,
            'id_categoria' => $this->id_categoria
        ]);
        $this->ResetUI();
        $this->emit('subcategory-updated', 'Subcategoría actualizada');
    }

    public function Destroy(Subcategorias $subcategoria)
    {
        $indicator = IndicadorCategorias::where('id_subcategoria', $subcategoria->id)->count();
        if ($indicator > 0) {
            $this->emit('subcat-indicators', 'No se puede eliminar la subcategoría porque tiene indicadores asociados');
            return;
        }
        $subcategoria->delete();
        $this->ResetUI();
        $this->emit('subcategory-deleted', 'Subcategoría eliminada');
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }
}
