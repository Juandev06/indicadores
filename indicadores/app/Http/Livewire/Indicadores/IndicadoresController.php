<?php

namespace App\Http\Livewire\Indicadores;

use App\Models\Config\Area;
use App\Models\User;
use App\Models\Aux\Periodos;
use App\Models\Variables\Variables;
use App\Models\Indicadores\Indicadores;
use App\Models\Indicadores\IndicadorValores;
use App\Models\Metas\Metas;
use App\Models\Config\Categorias;
use App\Models\Config\Subcategorias;
use App\Models\Indicadores\IndicadorCategorias;
use App\Models\Variables\VariableValores;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class IndicadoresController extends Component
{
    use WithFileUploads;
    use WithPagination;

    public
        $permisoModulo, // nombre del permiso de módulo actual
        $permisoEditarModulo, // nombre del permiso para edicion del modulo
        $usuarioAct, // usuario actual (con sesión activa)
        $areaUsuario, // área del usuario actual
        $PageTitle,
        $ComponentName,
        $pageTitle,
        $search,
        $pagination,

        $indicator_name,

        $formulaStored,

        $variable,
        $formulaExists,
        $category_id,
        $subcategory_id,

        $listSubcategory = [],
        $subcategories_list,
        $categories_list,
        $subcat_count,

        $categorias, // listado de categorias
        $subcategorias, // listado de subcategorias
        $categoriasSel, // categorias-subcategorias seleccionadas (array)
        $categoriasSelVal, // campo de categorias-subcategorias seleccionadas para validación

        // formulario de edición
        $periodos, // listado de periodos
        $areas, // listado de áreas
        $usuarios, // listado de usuarios
        $idIndicador, // id del indicador seleccionado
        $indicadorAct, // modelo del indicador seleccionado
        $idPeriodoIndicadorAct, // id del peridod del indicador actual, para validar si hay cambio en el update
        $categoriasIndAct, // listado de categorías del indicador actual
        $filtroVar,
        // formulario de fórmulas
        $formulaVars, // variables para la generación de fórmulas
        $periodosVar, // periodos para filtrar variables
        $periodoVar, // periodo seleccionado para filtrar variables
        // ficha técnica
        $archivoDoc;

    protected $listeners = [
        'StoreFormula',
        'deleteRow' => 'Destroy'
    ];

    public function mount()
    {
        $this->permisoModulo = 'config_indicadores';
        $this->permisoEditarModulo = 'config_indicadores_editar';
        $this->usuarioAct = Auth::user();
        $this->areaUsuario = Area::find($this->usuarioAct->id_area)->name;
        $this->PageTitle = 'Listado';
        $this->ComponentName = 'Indicadores';
        $this->pageTitle = 'Indicadores';
        $this->pagination = env('PAGINATION');

        $this->formulaStored = '';
        $this->subcategory_id = 0;
        $this->subcat_count = 0;

        $this->periodos = Periodos::where('estado', 'A')->orderBy('id')->get();
        $this->areas = Area::where('status', 'A')->orderBy('name')->get();
        $this->usuarios = User::where('status', 'A')->orderBy('name')->get();
        $this->idIndicador = 0;
        $this->indicadorAct = [
            'nombre' => '',
            'estado' => 'A', // estado activo por defecto
            'tipo' => 'N', // tipo numérico por defecto
            'id_periodo' => 1, // periodo mensual por defecto
            'id_usuario' => 0,
            'tolerancia' => env('TOLERANCIA_INDICADORES'),
            'tendencia' => 1, // tendencia creciente por defecto
            'ficha_tec_archivo' => null,
            'ficha_tec_carpeta' => null,
            'ext' => null,
            'mimetype' => null,
            'size' => null,
        ];
        $this->idPeriodoIndicadorAct = 0;
        $this->categorias = Categorias::where('estado', 'A')->orderBy('nombre')->get();
        $this->subcategorias = Subcategorias::where('estado', 'A')->orderBy('nombre')->get();
        foreach ($this->categorias as $categoria) {
            $this->categoriasSel['c' . $categoria->id] = [
                'id_subcategoria' => 0,
                'id_categoria' => $categoria->id
            ];
        }
        $this->filtroVar = '';
        $this->formulaVars = collect();
        $this->periodosVar = collect();
        $this->periodoVar = 0;
    }

    public function render()
    {
        // validar el ingreso del usuario al módulo actual
        if (! $this->usuarioAct->can($this->permisoModulo)) {
            return abort('403');
        }
        $strSearch = $this->search == '' ? false : ('%' . str_replace(' ', '%', $this->search) . '%');
        $indicadores = Indicadores::select(
            'indicadores.*',
            'aux_periodos.nombre as periodo',
            'areas.name as area',
            DB::raw("concat(users.name, ' ', users.lastName)  AS userName")
        )
            ->join('aux_periodos', 'aux_periodos.id', 'indicadores.id_periodo')
            ->join('users', 'users.id', 'indicadores.id_usuario')
            ->join('areas', 'areas.id', 'users.id_area')
            ->when($strSearch, function ($query, $strSearch) {
                return $query->whereRaw('concat(areas.name, aux_periodos.nombre, indicadores.id, indicadores.nombre, users.name, users.lastName) like ?', [$strSearch]);
            })
            ->orderBy('indicadores.nombre')
            ->paginate($this->pagination);

        return view('livewire.indicadores.indicadores', [
            'indicadores' => $indicadores,
            'variables' => Variables::where('estado', 'A')->orderBy('nombre')->get(),
        ])
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function ResetUI()
    {
        $this->formulaStored = '';
        $this->indicadorAct = [
            'nombre' => '',
            'estado' => 'A', // estado activo por defecto
            'tipo' => 'N', // tipo numérico por defecto
            'id_periodo' => 1, // periodo mensual por defecto
            'id_usuario' => 0,
            'tolerancia' => env('TOLERANCIA_INDICADORES'),
            'tendencia' => 1, // tendencia creciente por defecto
            'ficha_tec_archivo' => null,
            'ficha_tec_carpeta' => null,
            'ext' => null,
            'mimetype' => null,
            'size' => null,
        ];
        $this->idPeriodoIndicadorAct = 0;
        $this->idIndicador = 0;
        $this->periodosVar = collect();
        $this->periodoVar = 0;
        // reset de categorias
        foreach ($this->categorias as $categoria) {
            $this->categoriasSel['c' . $categoria->id] = [
                'id_subcategoria' => 0,
                'id_categoria' => $categoria->id
            ];
        }
        $this->filtroVar = '';
        $this->formulaVars = collect();
        
        $this->resetValidation();
        $this->resetPage();
    }

    public function ResetBuscarVar()
    {
        $this->filtroVar = '';
        $this->formulaVars = collect();
    }

    public function Edit($id)
    {
        $this->idIndicador = $id;
        $indicador = Indicadores::find($id);
        $this->indicadorAct = [
            'nombre' => $indicador->nombre,
            'estado' => $indicador->estado,
            'tipo' => $indicador->tipo,
            'id_periodo' => $indicador->id_periodo,
            'id_usuario' => $indicador->id_usuario,
            'tolerancia' => $indicador->tolerancia,
            'tendencia' => $indicador->tendencia,
            'ficha_tec_archivo' => null,
            'ficha_tec_carpeta' => null,
            'ext' => null,
            'mimetype' => null,
            'size' => null,
        ];
        // validar categorias del indicador actual
        $categoriasIndAct = IndicadorCategorias::where('id_indicador', $indicador->id)->get();
        foreach ($categoriasIndAct as $categoriaSel) {
            $this->categoriasSel['c' . $categoriaSel->id_categoria]['id_subcategoria'] = $categoriaSel->id_subcategoria;
        }
        $this->idPeriodoIndicadorAct = $indicador->id_periodo;
        $this->emit('show-modal');
    }

    public function Store()
    {
        $rules = [
            'indicadorAct.nombre' => 'required|unique:indicadores,nombre|min:2',
            'indicadorAct.id_usuario' => 'required|not_in:0',
            'indicadorAct.tolerancia' => 'required|numeric|integer|max:100|min:0',
        ];
        $messages = [
            'indicadorAct.nombre.required' => 'Debe ingresar el nombre para el indicador',
            'indicadorAct.nombre.unique' => 'Ya existe el indicador',
            'indicadorAct.nombre.min' => 'El nombre debe conteneder al menos  2 caracteres',
            'indicadorAct.id_usuario.not_in' => 'Seleccione el responsable del indicador',
            'indicadorAct.tolerancia.required' => 'Ingrese la tolerancia',
            'indicadorAct.tolerancia.numeric' => 'Debe ingresar un valor entero para la tolerancia',
            'indicadorAct.tolerancia.integer' => 'Debe ingresar un valor entero para la tolerancia',
            'indicadorAct.tolerancia.max' => 'El valor máximo para la tolerancia es 100',
            'indicadorAct.tolerancia.min' => 'El valor mínimo para la tolerancia es 0',
            'categoriasSelVal.required' => 'Debe seleccionar al menos una categoría',
        ];
        // validar si se seleccionó al menos una categoría
        foreach ($this->categoriasSel as $categoriaSel) {
            if ($categoriaSel['id_subcategoria'] == 0) {
                $this->categoriasSelVal = null;
                $rules['categoriasSelVal'] = 'required';
            } else {
                $this->categoriasSelVal = $categoriaSel['id_subcategoria'];
                break;
            }
        }
        $this->validate($rules, $messages);

        $indicador = Indicadores::create([
            'nombre' => $this->indicadorAct['nombre'],
            'estado' => $this->indicadorAct['estado'],
            'tipo' => $this->indicadorAct['tipo'],
            'id_periodo' => $this->indicadorAct['id_periodo'],
            'id_usuario' => $this->indicadorAct['id_usuario'],
            'formula' => '',
            'tolerancia' => $this->indicadorAct['tolerancia'],
            'tendencia' => $this->indicadorAct['tendencia'],
        ]);
        // crear las categorias seleccionadas
        foreach ($this->categoriasSel as $categoriaSel) {
            if ($categoriaSel['id_subcategoria'] != 0) {
                IndicadorCategorias::create([
                    'id_indicador' => $indicador->id,
                    'id_categoria' => $categoriaSel['id_categoria'],
                    'id_subcategoria' => $categoriaSel['id_subcategoria'],
                ]);
            }
        }
        $this->ResetUI();
        $this->indicator_name = 'Ingresar formula del indicador ' . $indicador->nombre;
        $this->emit('indicador-ok', 'Indicador agregado');
        $this->Formula($indicador->id);
    }

    public function Update()
    {
        $rules = [
            'indicadorAct.nombre' => [
                Rule::unique('indicadores', 'nombre')->ignore($this->indicadorAct['nombre'], 'nombre'),
                'required',
                'min:2',
            ],
            'indicadorAct.id_usuario' => 'required|not_in:0',
            'indicadorAct.tolerancia' => 'required|numeric|integer|max:100|min:0',
        ];
        $messages = [
            'indicadorAct.nombre.required' => 'Debe ingresar el nombre para el indicador',
            'indicadorAct.nombre.unique' => 'Ya existe el indicador',
            'indicadorAct.nombre.min' => 'El nombre debe conteneder al menos  2 caracteres',
            'indicadorAct.id_usuario.not_in' => 'Seleccione el responsable del indicador',
            'indicadorAct.tolerancia.required' => 'Ingrese la tolerancia',
            'indicadorAct.tolerancia.numeric' => 'Debe ingresar un valor entero para la tolerancia',
            'indicadorAct.tolerancia.integer' => 'Debe ingresar un valor entero para la tolerancia',
            'indicadorAct.tolerancia.max' => 'El valor máximo para la tolerancia es 100',
            'indicadorAct.tolerancia.min' => 'El valor mínimo para la tolerancia es 0',
            'categoriasSelVal.required' => 'Debe seleccionar al menos una categoría',
        ];
        // validar si se seleccionó al menos una categoría
        foreach ($this->categoriasSel as $categoriaSel) {
            if ($categoriaSel['id_subcategoria'] == 0) {
                $this->categoriasSelVal = null;
                $rules['categoriasSelVal'] = 'required';
            } else {
                $this->categoriasSelVal = $categoriaSel['id_subcategoria'];
                break;
            }
        }
        $this->validate($rules, $messages);
        // si hay cambio en el periodo, se recalculan los valores del indicador
        if ($this->idPeriodoIndicadorAct != $this->indicadorAct['id_periodo']) {
            IndicadorValores::where('id_indicador', $this->idIndicador)->delete();
        }
        $indicador = Indicadores::find($this->idIndicador);
        $indicador->update([
            'nombre' => $this->indicadorAct['nombre'],
            'estado' => $this->indicadorAct['estado'],
            'tipo' => $this->indicadorAct['tipo'],
            'id_periodo' => $this->indicadorAct['id_periodo'],
            'id_usuario' => $this->indicadorAct['id_usuario'],
            'tolerancia' => $this->indicadorAct['tolerancia'],
            'tendencia' => $this->indicadorAct['tendencia'],
        ]);
        // actualizar o crear las categorias seleccionadas
        $categoriasIndAct = IndicadorCategorias::where('id_indicador', $indicador->id)->get();
        foreach ($this->categoriasSel as $categoriaSel) {
            // existe la categoria?
            $categoriaIndAct = $categoriasIndAct->where('id_categoria', $categoriaSel['id_categoria'])->first();
            // si no existe y está seleccionado, crear nuevo registro
            if (is_null($categoriaIndAct) && $categoriaSel['id_subcategoria'] != 0) {
                IndicadorCategorias::create([
                    'id_indicador' => $indicador->id,
                    'id_categoria' => $categoriaSel['id_categoria'],
                    'id_subcategoria' => $categoriaSel['id_subcategoria'],
                ]);
            }
            // si existe y está desmarcado, eliminar registro
            if (!is_null($categoriaIndAct) && $categoriaSel['id_subcategoria'] == 0) {
                IndicadorCategorias::where('id_indicador', $indicador->id)
                    ->where('id_categoria', $categoriaSel['id_categoria'])
                    ->delete();
            }
        }
        $this->ResetUI();
        $this->emit('indicador-ok', 'Indicador Actualizado');
    }

    public function Destroy(Indicadores $indicador)
    {
        //Elimina la meta ingresada para el indicador
        Metas::where('id_indicador', $indicador->id)->delete();

        $indicador->delete();
        $this->ResetUI();
        $this->emit('msg-ok', 'Indicador Eliminado');
    }

    public function Formula($id)
    {
        $indicador = Indicadores::find($id);
        $this->formulaStored = $indicador->formula;
        $this->formulaExists = true;
        $this->idIndicador = $id;

        $this->periodosVar = Periodos::where('id', '<=', $indicador->id_periodo)->get();
        $this->periodoVar = $indicador->id_periodo;

        $this->emit('open-formula', 'Ingresar formula');
    }

    public function StoreFormula()
    {
        $indicador = Indicadores::find($this->idIndicador);
        $indicador->formula = $this->formulaStored;
        $indicador->save();
        // $this->calcularIndicador($indicador->id);
        $this->ResetUI();
        $this->emit('formula-updated', 'Formula actualizada');
    }

    public function abrirBuscarVariables()
    {
        $this->buscarVariables();
        $this->emit('show-variables');
    }

    public function limpiarFiltroVariables()
    {
        $this->filtroVar = '';
        $this->buscarVariables();
    }

    public function buscarVariables()
    {
        // periodos permitidos
        $filtro = $this->filtroVar == '' ? false : ('%' . str_replace(' ', '%', $this->filtroVar) . '%');
        $indicador = Indicadores::find($this->idIndicador);
        $filtroAnual = $this->periodoVar == 5;
        $this->formulaVars = Variables::select(
            'variables.*',
            'aux_periodos.nombre as periodo',
            'areas.name as area',
            DB::raw("concat(users.name, ' ', users.lastName)  AS responsable")
        )
            ->join('aux_periodos', 'aux_periodos.id', 'variables.id_periodo')
            ->join('users', 'users.id', 'variables.id_usuario')
            ->join('areas', 'areas.id', 'users.id_area')
            ->when($filtro, function ($query, $filtro) {
                return $query->where(
                    function ($query) use ($filtro) {
                        $query->where('variables.nombre', 'like', $filtro)
                            ->orWhereRaw('concat(variables.id, "") like ?', [$filtro])
                            ->orWhere('areas.name', 'like', $filtro)
                            ->orWhereRaw('concat(users.name, " ",users.lastName) like ?', [$filtro])
                            ->orWhere('aux_periodos.nombre', 'like', $filtro);
                    });
            })
            ->when($filtroAnual, function($query) use ($indicador) {
                $query->where('id_calendario', $indicador->id_calendario);
            })
            ->where('id_periodo', $this->periodoVar)
            ->orderBy('variables.nombre')
            ->limit(50)
            ->get();
    }

    public function AbrirCargaArchivo($id)
    {
        $this->idIndicador = $id;
        $indicador = Indicadores::find($id);
        $this->indicadorAct = [
            'nombre' => $indicador->nombre,
            'ficha_tec_archivo' => $indicador->ficha_tec_archivo,
            'ficha_tec_carpeta' => $indicador->ficha_tec_carpeta,
            'ext' => $indicador->ext,
            'mimetype' => $indicador->mimetype,
            'size' => $indicador->size,
        ];
        $this->emit('show-ficha-tec');
    }

    public function UploadFile()
    {
        $rules = ['archivoDoc' => 'required|mimes:' . env('EXTENSION_ARCHIVOS')];
        $messages = [
            'archivoDoc.required' => 'Debe seleccionar un archivo',
            'archivoDoc.mimes' => 'Solo puede seleccionar archivos con extensión ' . env('EXTENSION_ARCHIVOS'),
        ];
        $this->validate($rules, $messages);
        $extArchivo = $this->archivoDoc->getClientOriginalExtension();
        $mimeTypeArchivo = $this->archivoDoc->getMimeType();
        $sizeArchivo = $this->archivoDoc->getSize();
        $indicador = Indicadores::find($this->idIndicador);
        if (!is_null($this->indicadorAct['ficha_tec_archivo'])) {
            // actualizar el archivo
            $nombreArchivo = $indicador->ficha_tec_archivo;
            $folder = $indicador->ficha_tec_carpeta;
            // TODO: validar respuesta del servidor DO_S3
            $this->archivoDoc->storeAs($folder, $nombreArchivo, 'do_s3');

            $indicador->update([
                'ext' => $extArchivo,
                'mimetype' => $mimeTypeArchivo,
                'size' => $sizeArchivo,
            ]);
        } else {
            $folder = env('DO_S3_FOLDER') . '/' . date('Y') . '/' . date('m');
            $nombreArchivo = Hashids::encode($this->idIndicador) . '.' . $extArchivo;
            // TODO: validar respuesta del servidor DO_S3
            $this->archivoDoc->storeAs($folder, $nombreArchivo, 'do_s3');

            $indicador->update([
                'ficha_tec_archivo' => $nombreArchivo,
                'ficha_tec_carpeta' => $folder,
                'ext' => $extArchivo,
                'mimetype' => $mimeTypeArchivo,
                'size' => $sizeArchivo,
            ]);
        }
        $this->ResetUI();
        $this->emit('upload-ok', 'Se ha cargado el archivo exitosamente');
    }

    public function DescargarArchivo()
    {
        $indicador = Indicadores::find($this->idIndicador);
        $nombreArchivo = $indicador->nombre . '.' . $indicador->ext;
        $headers = [
            'Content-Type' => $indicador->mimetype,
            'Content-Disposition' => 'inline; filename="' . $nombreArchivo . '"'
        ];
        return Storage::disk('do_s3')->response(
            $indicador->ficha_tec_carpeta . '/' . $indicador->ficha_tec_archivo,
            $nombreArchivo,
            $headers
        );
    }
    // calcula y recalcula los resultados para el indicador
    public function calcularIndicador($idIndicador)
    {
        $indicador = Indicadores::find($idIndicador);
        $formula = $indicador->formula;
        $variablesRecalculo = [];
        $idsVariablesRecalculo = [];
        $formulaConError = false;
        // extraer los parámetros de la fórmula (string entre llaves {})
        // la cadena con las llaves incluidas se almacena en $parametros[0]
        preg_match_all('/\{(.*?)\}/', $formula, $parametros);
        foreach ($parametros[0] as $parametro) {
            // obtener la variable del parámetro
            preg_match_all('/\:([0-9]*?)\}/', $parametro, $variableAct);
            if (empty($variableAct[0])) {
                $formulaConError = true;
            } else {
                $idVariable = $variableAct[1][0];
                array_push($idsVariablesRecalculo, $idVariable);
                // obtener el id de la variable y reemplazar en formula
                $codVariable = 'v' . $idVariable;
                $variablesRecalculo[$codVariable] = $idVariable;
                $formula = str_replace($parametro, $codVariable, $formula);
            }
        }
        if ($formulaConError) {
            // no se pudo extrar una variable de alguno de los parámetros de la fórmula
            $this->emit('error-recalculo', 'Error en la creación de la fórmula.');
        } else {
            // recalcular resultados
            $valoresRecalculo = VariableValores::select('id_variable', 'valor', 'ano', 'mes')
                ->whereIn('id_variable', $idsVariablesRecalculo)
                ->get();
            $periodos = $valoresRecalculo->unique(function ($item) {
                return $item['ano'] . $item['mes'];
            });
            foreach ($periodos as $periodo) {
                $valoresIncompletos = false;
                // reemplazar fórmula por valores
                foreach ($variablesRecalculo as $parametro => $variableRecalculo) {
                    $valorRecalculo = $valoresRecalculo->where('ano', $periodo->ano)
                        ->where('mes', $periodo->mes)
                        ->where('id_variable', $variableRecalculo)
                        ->first();
                    if (is_null($valorRecalculo)) {
                        // formula incompleta
                        $valoresIncompletos = true;
                    } else {
                        $formula = str_replace($parametro, $valorRecalculo->valor, $formula);
                    }
                }
                // calcular el resultado de la fórmula para el periodo actual
                /*
                        // resolver ecuacion y almacenar en base de datos 
                        try {
                            $resultado = eval('return ' . $formulaAct['formula'] . ';');
                        } catch (\DivisionByZeroError $exception) {
                            //$exception->getMessage();
                        } catch (\ParseError $exception) {
                            //$exception->getMessage();
                        }
                        if (!isset($resultado)) {
                            $this->formulasConError = true;
                            $this->msgResultado[$formulaAct['idIndicador']] = [
                                'indicador' => $formulaAct['nombreIndicador'],
                                'msg' => 'Error fórmula mal creada o error  no se puede dividir por 0.',
                                'estado' => 'error',
                            ];
                        } else {
                            // crear o actualizar el resultado del indicador
                            $vlrIndicadorAct = IndicadorValores::where('id_indicador', $idIndicador)
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
                                $vlrIndicadorAct->update(['valor' => $resultado]);
                            }
                            $this->msgResultado[$formulaAct['idIndicador']] = [
                                'indicador' => $formulaAct['nombreIndicador'],
                                'msg' => 'Indicador calculado exitosamente.',
                                'estado' => 'ok',
                            ];
                        }
                */
                // valida si ya existe el valor calculado para el periodo actual
                $vlrCalculado = IndicadorValores::where('ano', $periodo->ano)->where('mes', $periodo->mes)->first();
                if (is_null($vlrCalculado)) {

                } else {
                    $vlrCalculado->obs = null;
                    $vlrCalculado->save();
                }
            }

            dd($valoresRecalculo);
        }
        
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }
}
