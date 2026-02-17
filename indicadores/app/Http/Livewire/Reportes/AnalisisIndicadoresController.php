<?php

namespace App\Http\Livewire\Reportes;

use App\Exports\AnalisisIndicadoresExport;
use App\Models\Config\Area;
use App\Models\Config\Categorias;
use App\Models\Config\Subcategorias;
use App\Models\Indicadores\IndicadorCategorias;
use App\Models\Indicadores\Indicadores;
use App\Models\Indicadores\IndicadorValores;
use Livewire\WithPagination;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;

class AnalisisIndicadoresController extends Component
{
    use WithPagination;
    public
        $permisoModulo, // nombre del permiso de módulo actual
        $permisoEditarModulo, // nombre del permiso para edicion del modulo
        $usuarioAct, // usuario actual (con sesión activa)
        $areaUsuario, // área del usuario actual
        $search,
        $pageTitle,
        $ComponentName,
        $pagination,
        $yearAct, // filtro de año seleccionado
        $yearIni, // año inicial
        $mesAct, // filtro de mes seleccionado
        $meses, // listado de meses
        $rolActual,
        $area, // filtro de area seleccionado
        $areas, // listado de areas
        $areasAll, // valida si se muestra la opción de todas las áreas

        $categorias, // lista de categorias disponibles
        $categoriaSel, // categoría seleccionada
        $subcategorias, // lista de subcategorias
        $subcategoriaSel, // subcategoria seleccionada
        $indicadoresSubcat, // listado de indicadores cuando se filtra por subcategoria

        $idValorIndicador, // id del valor actual del indicador
        $idUsuarioIndicadorAct, // id del usuario del indicador seleccionado
        $obsValorIndicador, // observacion del valor de un indicador
        $nombreIndicadorAct // nombre del indicador seleccionado para observación
    ;

    public function mount()
    {
        $this->permisoModulo = 'indicador_analisis';
        $this->permisoEditarModulo = 'indicador_analisis_editar';
        $this->usuarioAct = Auth::user();
        $this->areaUsuario = Area::find($this->usuarioAct->id_area)->name;
        $this->pageTitle = 'Análisis de Indicadores';
        $this->ComponentName = 'Valores indicadores';
        $this->rolActual = Role::find($this->usuarioAct->id_rol);
        if ($this->usuarioAct->hasRole(['Administrador', 'Auditor'])) {
            $this->area = 0;
            $this->areas = Area::orderBy('name')->get();
            $this->areasAll = true;
        } else {
            $this->area = $this->usuarioAct->id_area;
            $this->areas = Area::where('id', $this->area)->get();
            $this->areasAll = false;
        }
        $this->yearAct = date('Y');
        $this->yearIni = env('YEAR_INI');
        $this->mesAct = date('n');
        $this->meses = Config::get('constantes.meses');
        $this->pagination = env('PAGINATION');
        $this->idValorIndicador = 0;
        $this->obsValorIndicador = null;
        $this->nombreIndicadorAct = '';

        $this->categorias = Categorias::orderBy('nombre')->get();
        $this->categoriaSel = 0;
        $this->subcategorias = collect();
        $this->subcategoriaSel = 0;
    }

    public function render()
    {
        // validar el ingreso del usuario al módulo actual
        if (! $this->usuarioAct->can($this->permisoModulo)) {
            return abort('403');
        }
        if ($this->categoriaSel != 0) {
            $this->subcategorias = Subcategorias::where('id_categoria', $this->categoriaSel)->get();
        }
        $this->indicadoresSubcat = [];
        if ($this->subcategoriaSel != 0) {
            $this->indicadoresSubcat = IndicadorCategorias::select('id_indicador')
                ->where('id_subcategoria', $this->subcategoriaSel)
                ->get()
                ->map(function ($indCat) {
                    return $indCat->id_indicador;
                });
        }
        $strSearch = $this->search == '' ? false : ('%' . str_replace(' ', '%', $this->search) . '%');
        $filtroColaborador = $this->usuarioAct->hasRole(['Colaborador']);
        // TOD: para el rol Jefe de Area, permitir editar sus propios indicadores
        $data = DB::table('indicadores AS ind')
            ->leftJoin('indicador_valores AS ind_val', 'ind.id', 'ind_val.id_indicador')
            ->leftJoin('metas', function ($join) {
                $join->on('metas.id_indicador', 'ind_val.id_indicador');
                $join->on('ind_val.mes', 'metas.mes');
                $join->on('ind_val.ano', 'metas.anno');
            })
            ->join('users', 'users.id', 'ind.id_usuario')
            ->join('areas', 'areas.id', 'users.id_area')
            ->select(
                'ind_val.id as id_vlr',
                'ind_val.ano as ano',
                'ind_val.mes as mes',
                'ind_val.valor as ind_vlr',
                'ind_val.obs as obs',
                'ind.id as ind_id',
                'ind.tendencia',
                'ind.tolerancia',
                'ind.id_usuario',
                'ind.nombre as ind_name',
                'metas.value as meta_vlr',
                'ind.tipo as tipo_ind', // 1=numero, 2=porcentaje
                'areas.name as area_name'
            )
            //->whereNull('id_vlr')
            ->when($this->mesAct, function ($query, $mesAct) {
                $query->where('ind_val.mes', $mesAct);
            })
            ->when($strSearch, function ($query, $strSearch) {
                return $query->whereRaw('concat(areas.name, ind.nombre, users.name, users.lastName) like ?', [$strSearch]);
            })
            ->when($this->area, function ($query, $area) {
                $query->where('areas.id', $area);
            })
            ->when($this->subcategoriaSel, function ($query, $subcategoriaSel) {
                $query->whereIn('ind.id', $this->indicadoresSubcat);
            })
            ->when($filtroColaborador, function ($query, $filtroColaborador) {
                return $query->where('ind.id_usuario', $this->usuarioAct->id);
            })
            ->where('ano', $this->yearAct)
            ->orderBy('areas.name')
            ->orderBy('ind.nombre')
            ->orderBy('ind_val.mes')
            ->paginate($this->pagination);
            
        return view('livewire.reportes.analisisIndicadores', [
            'values' => $data,
        ])
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function ResetUI()
    {
        $this->idValorIndicador = 0;
        $this->obsValorIndicador = null;
        $this->resetValidation();
        $this->resetPage();
    }
    // mostrar modal para crear o editar una observación de un indicador
    public function ShowObsAnalisisIndicador($idValorIndicador)
    {
        $this->idValorIndicador = $idValorIndicador;
        $valorIndAct = IndicadorValores::find($this->idValorIndicador);
        $indicadorAct = Indicadores::find($valorIndAct->id_indicador);
        $this->nombreIndicadorAct = $indicadorAct->nombre;
        $this->obsValorIndicador = $valorIndAct->obs;
        $this->idUsuarioIndicadorAct = $indicadorAct->id_usuario;
        $this->emit('show-obs');
    }
    // función para editar observación del valor calculado de un indicador
    public function ObsAnalisisIndicador()
    {
        IndicadorValores::find($this->idValorIndicador)
            ->update(['obs' => $this->obsValorIndicador]);
        $this->emit('obs-ok', 'Observación guardada correctamente');
    }

    public function reporteExcel()
    {
        // $tipoFiltroAct = $this->tipoFiltroSel == 'todos' ? 0 : $this->tipoFiltroSel;
        // $filtroAct = $this->tipoFiltroSel == 'todos' ? '' : ('(filtro ' . $this->tiposFiltro[$this->tipoFiltroSel]['nombre']) . ') ';
        $nombreArchivo = 'Analisis de indicadores ' . $this->yearAct . '-' . $this->mesAct . '.xlsx';
        $datos = new AnalisisIndicadoresExport([$this->yearAct, $this->mesAct, $this->area]);
        return Excel::download($datos, $nombreArchivo);
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }
}
