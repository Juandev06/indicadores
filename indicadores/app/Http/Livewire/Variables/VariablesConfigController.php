<?php

namespace App\Http\Livewire\Variables;

use App\Models\Aux\Periodos;
use App\Models\Config\Area;
use App\Models\User;
use App\Models\Indicadores\Indicadores;
use App\Models\Variables\Variables;
use App\Models\Variables\VariableValores;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;

class VariablesConfigController extends Component
{
    use WithPagination;

    public
        $permisoModulo, // nombre del permiso de módulo actual
        $permisoEditarModulo, // nombre del permiso para edicion del modulo
        $usuarioAct, // usuario actual (con sesión activa)
        $areaUsuario, // área del usuario actual
        $pageTitle,
        $ComponentName, // variable para mostrar en sidebar
        $search,
        $pagination,
        $listaIndicadores, // listado de indicadores asociados a una variable

        // datos variable
        $idVariableAct,
        $nombreVariableAct, // nombre de la variable seleccionada
        $nombre, // nombre de la variable para el input de nombre
        $id_usuario, // id del usuario responsable de la variable
        $id_periodo, // id del periodo
        $estado, // A:activo, I: inactivo
        $obs, // observaciones o descripción de la variable
        $tipo, // N: numérico, P: porcentual
        $periodos, // listado de periodos
        $disableInput;

    protected $listeners = ['deleteRow' => 'Destroy'];

    public function mount()
    {
        $this->permisoModulo = 'config_variables';
        $this->permisoEditarModulo = 'config_variables_editar';
        $this->usuarioAct = Auth::user();
        $this->areaUsuario = Area::find($this->usuarioAct->id_area)->name;
        $this->pageTitle = 'Variables';
        $this->ComponentName = 'Variables';
        $this->pagination = env('PAGINATION');
        $this->idVariableAct = 0;
        $this->nombreVariableAct = '';
        $this->nombre = '';
        $this->estado = 'A';
        $this->tipo = 'N';
        $this->disableInput = '';
        $this->id_periodo = Periodos::first()->id;
        $this->id_usuario = 0;
        $this->periodos = Periodos::where('estado', 'A')->orderBy('id')->get();
        $this->listaIndicadores = collect();
    }

    public function render()
    {
        // validar el ingreso del usuario al módulo actual
        if (! $this->usuarioAct->can($this->permisoModulo)) {
            return abort('403');
        }
        $strSearch = $this->search == '' ? false : ('%' . str_replace(' ', '%', $this->search) . '%');
        $variables = Variables::select(
            'variables.*',
            'aux_periodos.nombre as periodo',
            'areas.name as area',
            DB::raw("concat(users.name, ' ', users.lastName)  AS userName")
        )
            ->join('aux_periodos', 'aux_periodos.id', 'variables.id_periodo')
            ->join('users', 'users.id', 'variables.id_usuario')
            ->join('areas', 'areas.id', 'users.id_area')
            ->when($strSearch, function ($query, $strSearch) {
                return $query->whereRaw('concat(areas.name, aux_periodos.nombre, variables.id, variables.nombre, users.name, users.lastName) like ?', [$strSearch]);
            })
            ->orderBy('variables.nombre')
            ->orderBy('areas.name')
            ->paginate($this->pagination);

        return view('livewire.variables.variables', [
            'variables' => $variables,
            'users' => User::where('status', 'A')->orderBy('name')->get(),
        ])
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function ResetUI()
    {
        $this->nombre = '';
        $this->tipo = 'N';
        $this->estado = 'A';
        $this->idVariableAct = 0;
        $this->id_usuario = 0;
        $this->disableInput = '';
        $this->id_periodo = Periodos::first()->id;
        $this->listaIndicadores = collect();
        $this->resetValidation();
        $this->resetPage();
    }

    public function Store()
    {
        //Valida que el nombre de variable no contenga los caracteres {, }, :
        preg_match_all('/[{}:]/', $this->nombre, $variables);
        if (sizeof($variables[0]) > 0) {
            $this->emit('error-variableName', 'El nombre de la variable no puede contener los siguiente 
            caracteres: {, }, :');
            return;
        }
        $rules = [
            'nombre' => 'required|unique:variables|min:2',
            'tipo' => 'required|not_in:0',
            'id_periodo' => 'required',
            'id_usuario' => 'required|not_in:0'
        ];
        $messages = [
            'nombre.required' => 'Debe ingresar el nombre para la variable',
            'nombre.unique' => 'Ya existe la variable',
            'nombre.min' => 'El nombre debe conteneder al menos  2 carácteres',
            'nombre.regex' => 'El nombre contiene caracteres no permitidos',
            'tipo.not_in' => '0',
            'id_usuario.not_in' => '0'

        ];
        $this->validate($rules, $messages);
        Variables::create([
            'nombre' => $this->nombre,
            'estado' => 'A',
            'tipo' => $this->tipo,
            'id_periodo' => $this->id_periodo,
            'id_usuario' => $this->id_usuario
        ]);

        $this->ResetUI();
        $this->emit('variable-added', 'Variable agregada');
    }

    public function Edit($id)
    {
        $variableAct = Variables::find($id);
        $this->idVariableAct = $variableAct->id;
        $this->nombreVariableAct = $variableAct->nombre;
        $this->nombre = $variableAct->nombre;
        $this->estado = $variableAct->estado;
        $this->tipo = $variableAct->tipo;
        $this->id_periodo = $variableAct->id_periodo;
        $this->id_usuario = $variableAct->id_usuario;
        $indicadores = Indicadores::where('formula', 'like', '%:' . $this->idVariableAct . '}%')->first();
        if (!is_null($indicadores)) {
            $this->disableInput = 'disabled';
            $this->emit('show-modal', 'Editar variable');
            return back()->with('variableFail', 'No se puede inactivar. La variable se encuentra asociada a uno o mas indicadores.')
                ->with('variableFailTimes', 'No se puede modificar la periodicidad. La variable se encuentra asociada a uno o mas indicadores.');
        }
        $this->emit('show-modal', 'Editar variable');
    }

    public function Update()
    {
        //Valida que el nombre de variable no contenga los caracteres {, }, :
        preg_match_all('/[{}:]/', $this->nombre, $variables);
        if (sizeof($variables[0]) > 0) {
            $this->emit('error-variableName', 'El nombre de la variable no puede contener los siguiente 
            caracteres: {, }, :');
            return;
        }

        $rules = [
            'nombre' => "required|min:2",
            'estado' => 'required',
            'tipo' => 'required|not_in:0',
            'id_periodo' => 'required|not_in:0',
            'id_usuario' => 'required|not_in:0'
        ];
        $messages = [
            'nombre.required' => 'Debe ingresar el nombre del área',
            'nombre.unique' => 'Ya existe el área',
            'nombre.min' => 'El nombre debe conteneder al menos  2 carácteres',
            'tipo.not_in' => 'Seleccione el tipo',
            'id_periodo.not_in' => 'Seleccione la periodicidad',
            'id_usuario.not_in' => 'Seleccione el responsable'
        ];
        $this->validate($rules, $messages);
        $variable = Variables::find($this->idVariableAct);
        $variable->update([
            'nombre' => $this->nombre,
            'estado' => $this->estado,
            'tipo' => $this->tipo,
            'id_periodo' => $this->id_periodo,
            'id_usuario' => $this->id_usuario
        ]);

        $this->ResetUI();
        $this->emit('variable-updated', 'Variable agregada');
    }

    public function Destroy(Variables $variable)
    {
        // buscar la variable actual en las formulas de indicadores
        $indicadores = Indicadores::where('formula', 'like', '%:' . $variable->id . '}%')->count();
        if ($indicadores > 0) {
            $this->emit('variable-indicators', 'No se puede eliminar. La variable tiene uno o mas indicadores asociados.');
            return;
        }
        //Se buscan los valores de las variables 
        VariableValores::where('id_variable', $variable->id)->delete();
        $variable->delete();
        $this->ResetUI();
        $this->emit('variable-deleted', 'Variable eliminada');
    }

    public function ListarIndicadores($idIndicador)
    {
        $variableAct = Variables::find($idIndicador);
        $this->idVariableAct = $variableAct->id;
        $this->nombreVariableAct = $variableAct->id . ' - ' . $variableAct->nombre;
        $this->nombre = $variableAct->nombre;
        $this->listaIndicadores = Indicadores::where('formula', 'like', '%:' . $this->idVariableAct . '}%')
            ->orderBy('nombre')
            ->get();
        $this->emit('lista-indicadores');
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }
}
