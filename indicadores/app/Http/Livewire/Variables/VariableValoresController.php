<?php

namespace App\Http\Livewire\Variables;

use App\Exports\ReporteVariablesExport;
use App\Models\Aux\PeriodoDets;
use App\Models\Aux\Periodos;
use App\Models\Config\Area;
use App\Models\Variables\VariablePeriodoHabilitado;
use App\Models\Variables\Variables;
use App\Models\Variables\VariableValores;
use App\Models\Indicadores\Indicadores;
use App\Models\Indicadores\IndicadorValores;
use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class VariableValoresController extends Component
{
    use WithPagination;

    public
        $permisoModulo, // nombre del permiso de módulo actual
        $permisoEditarModulo, // nombre del permiso para edicion del modulo
        $usuarioAct, // usuario actual (con sesión activa)
        $areaUsuario, // área del usuario actual
        $pageTitle,
        $pageSubtitle,
        $PageTitle,
        $ComponentName,
        $pagination,
        $search,

        $listaIndicadores, // listado de indicadores asociados a una variable
        $idVariableAct, // id de la variable seleccionada
        $listaVariablesRep, // listado de ids de variables para reporte
        $nombreVariableAct, // nombre de la variable seleccionada (id + nombre)
        $tipoIngreso, // define el tipo de acción del formulario: store, edit
        $valor, // valor de la variable seleccionada
        $tipoValor, // tipo de valor a ingresar o editar (N: numérico, P: porcentual)
        $fechaValor, // fecha de la última actualización del valor de la variable seleccionada
        $usuarioActRol, // rol del usuario actual
        $periodoAct, // periodo actvo (ver tabla variable_periodo_habilitado)
        $soloPendientes, // filtra las variables que estén sin valor ingresado en el periodo activo
        $msgResultado, // array con el resultado del cálculo de los indicadores calculador por la variable
        $formulasConError // valida si hubo errores en la actualización de indicadores true/false
    ;

    protected $listeners = ['deleteRow' => 'Destroy'];

    public function mount()
    {
        $this->permisoModulo = 'variable_ingresar';
        $this->permisoEditarModulo = 'variable_ingresar_editar';
        $this->usuarioAct = Auth::user();
        $this->areaUsuario = Area::find($this->usuarioAct->id_area)->name;
        $this->pageTitle = 'Ingresar Valores Variables';
        $this->periodoAct = VariablePeriodoHabilitado::where('estado', 'A')->first();
        $this->pageSubtitle = is_null($this->periodoAct) ? 'No hay periodos habilitados' :
            strtoupper(Config::get('constantes.meses')[$this->periodoAct->mes]) . ' - ' . $this->periodoAct->ano;
        $this->ComponentName = 'Valores Variables';
        $this->pagination = env('PAGINATION');
        $this->search == '';
        $this->usuarioActRol = Role::find($this->usuarioAct->id_rol);
        $this->valor = '';
        $this->tipoValor = 'N';
        $this->fechaValor = '';
        $this->tipoIngreso = 'store';
        $this->soloPendientes = false;
        $this->msgResultado = [];
        $this->formulasConError = false;
        $this->idVariableAct = 0;
        $this->nombreVariableAct = '';
        $this->listaIndicadores = collect();
        $this->listaVariablesRep = [];
    }

    public function render()
    {
        // validar el ingreso del usuario al módulo actual
        if (! $this->usuarioAct->can($this->permisoModulo)) {
            return abort('403');
        }
        if (!is_null($this->periodoAct)) {
            // listado de filtros
            $strSearch = $this->search == '' ? false : ('%' . str_replace(' ', '%', $this->search) . '%');
            $filtroArea = $this->usuarioAct->hasRole(['Jefe de Area']);
            $filtroColaborador = $this->usuarioAct->hasRole(['Colaborador']);
            $filtroSoloPendientes = $this->soloPendientes;
            // listado de las variables con valor en el periodo actual
            $valoresVariables = VariableValores::where('ano', $this->periodoAct->ano)
                ->where('mes', $this->periodoAct->mes)
                ->get();
            $idsValoresVariables = $valoresVariables->map(function ($vlrVariable) {
                return $vlrVariable->id_variable;
            });

            // filtrar los periodos que aplican para el mes activo para ingreso de variables
            $periodosCalendario = PeriodoDets::select(DB::raw('concat(id_calendario,id_periodo) as cal_per'))
                ->where('mes', $this->periodoAct->mes)
                ->where('aplica', 1)
                ->get()
                ->pluck('cal_per')
                ->toArray();

            $variables = Variables::select(DB::raw(
                'variables.id, 
                variables.nombre, 
                variables.tipo, 
                variables.obs,
                variables.id_calendario,
                aux_periodos.nombre as periodo,
                concat(users.name, " ", users.lastName) as responsable',
            ))
                ->leftJoin('users', 'users.id', 'variables.id_usuario')
                ->leftJoin('aux_periodos', 'aux_periodos.id', 'variables.id_periodo')
                ->whereIn(DB::raw('concat(id_calendario, id_periodo)'), $periodosCalendario)
                ->when($filtroArea, function ($query, $filtroArea) {
                    return $query->where('users.id_area', $this->usuarioAct->id_area);
                })
                ->when($filtroColaborador, function ($query, $filtroColaborador) {
                    return $query->where('id_usuario', $this->usuarioAct->id);
                })
                ->when($strSearch, function ($query, $strSearch) {
                    return $query->where(
                        function ($query) use ($strSearch) {
                            $query->where('variables.id', 'like', $strSearch)
                                ->orWhere('variables.nombre', 'like', $strSearch)
                                ->orWhere('aux_periodos.nombre', 'like', $strSearch)
                                ->orWhereRaw('concat(users.name, users.lastName) like ?', [$strSearch]);
                        }
                    );
                })
                ->when($filtroSoloPendientes, function ($query, $filtroSoloPendientes) use ($idsValoresVariables) {
                    return $query->whereNotIn('variables.id', $idsValoresVariables);
                })
                ->orderBy('variables.nombre')
                ->paginate($this->pagination);
            $this->listaVariablesRep = $variables->pluck('id')->toArray();
        } else {
            $variables = collect();
            $valoresVariables = collect();
            $this->listaVariablesRep = [];
        }

        return view('livewire.variables.variableValores', [
            'variables' => $variables,
            'valoresVariables' => $valoresVariables,
        ])->extends('layouts.theme.app')
            ->section('content');
    }

    public function ResetUI()
    {
        $this->idVariableAct = 0;
        $this->nombreVariableAct = '';
        $this->valor = '';
        $this->tipoValor = 'N';
        $this->fechaValor = '';

        $this->listaIndicadores = collect();
        $this->resetValidation();
        $this->resetPage();
    }

    public function Store()
    {
        $rules = ['valor' => 'required|numeric'];
        $messages = [
            'valor.required' => 'Debe ingresar un valor',
            'valor.numeric' => 'Debe ingresar un valor numérico'
        ];
        $this->validate($rules, $messages);

        VariableValores::create([
            'valor' => $this->valor,
            'ano' => $this->periodoAct->ano,
            'mes' => $this->periodoAct->mes,
            'id_usuario' => $this->usuarioAct->id,
            'id_variable' => $this->idVariableAct,
        ]);

        // actualizar indicadores que contengan la variable actual
        $this->calcularIndicadores();

        $this->ResetUI();
        $this->emit('valor-ok', 'Valor de la variable agregado');
    }

    public function Edit($id, $tipo)
    {
        $this->tipoIngreso = $tipo;
        $variable = Variables::find($id);
        $vlrVariable = VariableValores::where('id_variable', $id)
            ->where('mes', $this->periodoAct->mes)
            ->where('ano', $this->periodoAct->ano)
            ->first();
        $this->idVariableAct = $id;
        $this->nombreVariableAct = $variable->id . ' - ' . $variable->nombre;
        $this->valor = is_null($vlrVariable) ? '' : $vlrVariable->valor;
        $this->fechaValor = is_null($vlrVariable) ? '' : $vlrVariable->updated_at;
        $this->tipoValor = $variable->tipo;
        $this->emit('show-modal');
    }

    public function Update()
    {
        $rules = ['valor' => 'required|numeric'];
        $messages = [
            'valor.required' => 'Debe ingresar un valor',
            'valor.numeric' => 'Debe ingresar un valor numerico'
        ];
        $this->validate($rules, $messages);
        $dataVariable = VariableValores::where('id_variable', $this->idVariableAct)
            ->where('mes', $this->periodoAct->mes)
            ->where('ano', $this->periodoAct->ano)
            ->update([
            'id_usuario' => $this->usuarioAct->id,
            'valor' => $this->valor,
        ]);
        $this->calcularIndicadores();
        $this->ResetUI();
        $this->emit('valor-ok', 'Valor de la variable actualizado');
    }

    public function Destroy($id)
    {
        // buscar los resultados de indicadores donde las fórmulas contengan la variable actual en el periodo activo
        // y eliminarlos
        $indicadores = Indicadores::where('formula', 'like', ('%:' . $id . '}%'))->get();
        $idsIndicadores = $indicadores->map(function($ind){
            return $ind->id;
        });
        $valoresIndicadores = IndicadorValores::whereIn('id_indicador', $idsIndicadores)
            ->where('ano', $this->periodoAct->ano)
            ->where('mes', $this->periodoAct->mes)
            ->delete();

        $valorVariable = VariableValores::where('id_variable', $id)
            ->where('ano', $this->periodoAct->ano)
            ->where('mes', $this->periodoAct->mes)
            ->first();
        $valorVariable->delete();
        $this->ResetUI();
        $this->emit('msg-ok', 'Valor de variable eliminado');
    }

    // Revisa todas las formulas que contengan la variable actual
    public function calcularIndicadores()
    {
        $indicadores = Indicadores::where('formula', 'like', ('%:' . $this->idVariableAct . '}%'))->get();
        $this->msgResultado = [];
        $formulasParaCalcular = [];
        $this->formulasConError = false;
        foreach ($indicadores as $indicador) {
            $formula = $indicador->formula;
            $idIndicador = $indicador->id;
            $idCalendario = $indicador->id_calendario;
            $periodoIndicador = Periodos::find($indicador->id_periodo);
            // calcular meses del periodo del indicador. Se utiliza si el periodo de la variable es diferente
            $mesesIndicador = [];
            $mesInd = $this->periodoAct->mes;
            $yearInd = $this->periodoAct->ano;
            for ($i=0; $i < $periodoIndicador->cant_meses; $i++) { 
                $mesesIndicador[] = $yearInd . $mesInd;
                $mesInd = $mesInd - 1;
                if ($mesInd == 0 ) {
                    $mesInd = 12;
                    $yearInd = $yearInd - 1;
                }
            }
            // periodo detalle actual:
            $formulaIncompleta = false;
            $formulaConError = false;
            // extraer los parámetros de la fórmula (string entre llaves {})
            // la cadena con las llaves incluidas se almacena en $parametros[0]
            preg_match_all('/\{(.*?)\}/', $formula, $parametros);
            foreach ($parametros[0] as $parametro) {
                // obtener la variable del parámetro
                preg_match_all('/\:([0-9]*?)\}/', $parametro, $variableAct);
                if (empty($variableAct[0])) {
                    $formulaConError = true;
                    $this->formulasConError = true;
                } else {
                    $idVariable = $variableAct[1];
                    // validar si el periodo de la variable es igual al periodo del indicador
                    $variableFxAct = Variables::find($idVariable[0]);
                    if (is_null($variableFxAct)) {
                        $formulaConError = true;
                        $this->formulasConError = true;
                    } else {
                        if ($variableFxAct->id_periodo == $indicador->id_periodo) {
                            // obtener el valor de la variable y reemplazar en formula
                            $vlrVariable = VariableValores::select('valor')
                                ->where('id_variable', $idVariable)
                                ->where('mes', $this->periodoAct->mes)
                                ->where('ano', $this->periodoAct->ano)
                                ->first();
                            $valorVar = is_null($vlrVariable) ? null : $vlrVariable->valor;
                        } else {
                            // analizar la sumatoria de variables
                            $vlrVariable = VariableValores::select(DB::raw('mes, sum(valor) as valor'))
                                ->where('id_variable', $idVariable)
                                ->whereIn(DB::raw('concat(ano, mes)'), $mesesIndicador)
                                ->groupBy('mes')
                                ->get();
                            // se analiza la cantidad de registros requeridos, si es diferente no se calcula el indicador
                            $periodoVariable = Periodos::find($variableFxAct->id_periodo);
                            $cantRegistros = $periodoIndicador->cant_meses / $periodoVariable->cant_meses;
                            $valorVar = $vlrVariable->count() == $cantRegistros ? $vlrVariable->sum('valor') : null;
                        }
    
                        if (is_null($valorVar)) {
                            $formulaIncompleta = true;
                        } else {
                            $formula = str_replace($parametro, $valorVar, $formula);
                        }
                    }
                }
            }
            if ($formulaConError) {
                // no se pudo extrar una variable de alguno de los parámetros de la fórmula
                $this->msgResultado[$indicador->id] = [
                    'indicador' => $indicador->id . ' - ' . $indicador->nombre, 
                    'msg' => 'Error en la creación de la fórmula.',
                    'estado' => 'error',
                ];
            } else if ($formulaIncompleta) {
                // la fórmula está bien, pero no se puede calcular porque faltan variables por ingresar
                $this->msgResultado[$indicador->id] = [
                    'indicador' => $indicador->id . ' - ' . $indicador->nombre,
                    'msg' => 'Indicador no se puede calcular porque faltan variables por ingresar.',
                    'estado' => 'ok',
                ];
            } else {
                // se pasa la fórmula al array para calcular indicadores
                $formulasParaCalcular[] = [
                    'formula' => $formula, 
                    'idIndicador' => $indicador->id,
                    'nombreIndicador' => $indicador->id . ' - ' . $indicador->nombre, 
                ];
            }
        }
        // calcular todas las fórmulas
        foreach($formulasParaCalcular as $formulaAct) {
            // resolver ecuacion y almacenar en base de datos
            try {
                $resultado = eval('return ' . $formulaAct['formula'] . ';');                    
            } catch (\DivisionByZeroError $exception) {
                //$exception->getMessage();
            } catch (\ParseError $exception) {
                //$exception->getMessage();
            }
            if(!isset($resultado)) {
                $this->formulasConError = true;
                $this->msgResultado[$formulaAct['idIndicador']] = [
                    'indicador' => $formulaAct['nombreIndicador'],
                    'msg' => 'Error fórmula mal creada o error  no se puede dividir por 0.',
                    'estado' => 'error',
                ];
            } else {
                // crear o actualizar el resultado del indicador
                $vlrIndicadorAct = IndicadorValores::where('id_indicador', $formulaAct['idIndicador'])
                    ->where('ano', $this->periodoAct->ano)
                    ->where('mes', $this->periodoAct->mes)
                    ->first();
                if (is_null($vlrIndicadorAct)) {
                    IndicadorValores::create([
                        'id_usuario' => $this->usuarioAct->id,
                        'id_indicador' => $formulaAct['idIndicador'],
                        'ano' => $this->periodoAct->ano,
                        'mes' => $this->periodoAct->mes,
                        'valor' => $resultado,
                    ]);
                } else {
                    $vlrIndicadorAct->update([
                        'valor' => $resultado,
                        'obs' => '',
                    ]);
                }
                $this->msgResultado[$formulaAct['idIndicador']] = [
                    'indicador' => $formulaAct['nombreIndicador'],
                    'msg' => 'Indicador calculado exitosamente.',
                    'estado' => 'ok',
                ];
            }
        }
    }

    public function ListarIndicadores($idIndicador)
    {
        $variableAct = Variables::find($idIndicador);
        $this->idVariableAct = $variableAct->id;
        $this->nombreVariableAct = $variableAct->id . ' - ' . $variableAct->nombre;
        $this->listaIndicadores = Indicadores::where('formula', 'like', '%:' . $this->idVariableAct . '}%')
            ->orderBy('nombre')
            ->get();
        $this->emit('lista-indicadores-rel');
    }

    public function reporteExcel()
    {
        $nombreArchivo = 'Analisis de variables ' . $this->pageSubtitle . '.xlsx';
        $datos = new ReporteVariablesExport([
            $this->listaVariablesRep, 
            $this->periodoAct->ano, 
            $this->periodoAct->mes]
        );
        return Excel::download($datos, $nombreArchivo);
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }
}
