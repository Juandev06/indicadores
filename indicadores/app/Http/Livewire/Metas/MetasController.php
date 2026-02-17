<?php

namespace App\Http\Livewire\Metas;

use App\Models\Aux\PeriodoDets;
use App\Models\Config\Area;
use Livewire\Component;
use App\Models\Indicadores\Indicadores;
use App\Models\Metas\MetaPeriodoHabilitado;
use Livewire\WithPagination;
use App\Models\Metas\Metas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MetasController extends Component
{
    use WithPagination;

    public
        $permisoModulo, // nombre del permiso de módulo actual
        $permisoEditarModulo, // nombre del permiso para edicion del modulo
        $permisoHabilitarMeta, // nombre del permiso para habilitar el ingreso de metas
        $usuarioAct, // usuario actual (con sesión activa)
        $areaUsuario, // área del usuario actual

        $area, // filtro de area para los roles diferentes a administrador y auditor

        $yearIni, // año inicial (desde .env)
        $yearFin, // año final para mostrar en vista
        $yearSel, // año seleccionado
        $yearSelEstado, // estado del año seleccionado
        $meses, // listado de meses
        $valoresMeta, // listado de valores de la meta de un indicador para un año
        $indicadorSel, // indicador seleccionado
        
        $search,
        $pageTitle,
        $pageSubtitle,
        $pagination;

    public function mount()
    {
        $this->permisoModulo = 'metas';
        $this->usuarioAct = Auth::user();
        $this->areaUsuario = Area::find($this->usuarioAct->id_area)->name;
        $this->permisoEditarModulo = $this->usuarioAct->can('metas_editar');
        $this->permisoHabilitarMeta = $this->usuarioAct->can('metas_habilitar');
        $this->pageTitle = 'Metas';
        $this->search == '';

        $this->yearSel = date('Y');

        $this->yearIni = env('YEAR_INI');
        $this->yearFin = date('Y') + 1;
        $this->meses = config('constantes.meses');
        $this->valoresMeta = [];
        $this->indicadorSel = collect();

        $this->pagination = env('PAGINATION');
        
        
        if ($this->usuarioAct->hasRole(['Administrador', 'Auditor'])) {
            $this->area = 0;
        } else {
            $this->area = $this->usuarioAct->id_area;
        }
    }

    public function render()
    {
        // validar el ingreso del usuario al módulo actual
        if (!$this->usuarioAct->can($this->permisoModulo)) {
            return abort('403');
        }
        $estadoPeriodoActual = MetaPeriodoHabilitado::where('ano', $this->yearSel)->first();
        $this->yearSelEstado = is_null($estadoPeriodoActual) ? 'I' : $estadoPeriodoActual->estado;

        $strSearch = $this->search == '' ? false : ('%' . str_replace(' ', '%', $this->search) . '%');
        $filtroUsuario = $this->usuarioAct->hasRole(['Administrador', 'Auditor']) ? 0 : $this->usuarioAct->id;
        $indicadorMetas = Metas::select('id_indicador', DB::raw('count(*) as cant'))
            ->where('anno', $this->yearSel)
            ->groupBy('id_indicador');
        $indicadores = Indicadores::select(
            'aux_periodos.nombre as periodo',
            'indicadores.nombre as nombre',
            'indicadores.id_periodo',
            DB::raw('concat(users.name, " ", users.lastName) as usuario'),
            'indicadores.id as id_indicador',
            'metas.cant as metas_cant',
        )
            ->join('aux_periodos', 'indicadores.id_periodo', 'aux_periodos.id')
            ->join('users', 'indicadores.id_usuario', 'users.id')
            ->leftJoinSub($indicadorMetas, 'metas', function ($join) {
                $join->on('indicadores.id', 'metas.id_indicador');
            })
            ->when($strSearch, function ($query, $strSearch) {
                return $query->whereRaw('concat(users.name, users.lastName, aux_periodos.nombre, indicadores.nombre) like ?', [$strSearch]);
            })
            ->when($filtroUsuario, function ($query, $filtroUsuario) {
                return $query->where('indicadores.id_usuario', $filtroUsuario);
            })
            ->orderBy('indicadores.nombre')
            ->paginate($this->pagination);

        return view('livewire.metas.metas', [
            'indicadores' => $indicadores
        ])
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function ResetUI()
    {
        $this->valoresMeta = [];
        $this->indicadorSel = collect();
    }

    // mostrar datos en formulario para crear o editar las metas para un indicador
    public function VerCrearOEditar($idIndicador)
    {
        $this->indicadorSel = Indicadores::find($idIndicador);
        $periodoDets = PeriodoDets::where('id_periodo', $this->indicadorSel->id_periodo)
            ->where('id_calendario', $this->indicadorSel->id_calendario)
            ->where('aplica', 1)
            ->get();
        // registros de metas para el indicador seleccionado
        $regMetas = Metas::where('id_indicador', $idIndicador)
            ->where('anno', $this->yearSel)
            ->get();
        if (!$regMetas->isEmpty()) {
            foreach ($regMetas as $regMeta) {
                $periodoDet = $periodoDets->where('mes', $regMeta->mes)->first();
                $this->valoresMeta[$regMeta->id] = [
                    'valor' => $regMeta->value,
                    'mes' => $regMeta->mes,
                    'id_periodo' => $regMeta->id_periodo,
                    'periodo_det' => $periodoDet->nombre,
                ];
            }
        } else {
            foreach ($periodoDets as $periodoDet) {
                $this->valoresMeta[$periodoDet->id] = [
                    'valor' => '',
                    'mes' => $periodoDet->mes,
                    'id_periodo' => $periodoDet->id_periodo,
                    'periodo_det' => $periodoDet->nombre,
                ];
            }
        }
        $this->emit('show-modal');
    }

    // crear o editar las metas para un indicador
    public function CrearOEditar()
    {
        $msg = '';
        // existen las metas para el periodo?
        $cantMetas = Metas::where('anno', $this->yearSel)
            ->where('id_indicador', $this->indicadorSel->id)
            ->count();
        if ($cantMetas > 0) {
            // las metas ya existen, actualizar datos
            foreach ($this->valoresMeta as $idMeta => $valorMeta) {
                Metas::where('id', $idMeta)
                    ->update(['value' => $valorMeta['valor']]);
            }
            $msg = 'Metas actualizadas exitosamente';
        } else {
            // crear las nuevas metas
            foreach ($this->valoresMeta as $valorMeta) {
                Metas::create([
                    'anno' => $this->yearSel,
                    'mes' => $valorMeta['mes'],
                    'value' => $valorMeta['valor'],
                    'id_indicador' => $this->indicadorSel->id,
                    'id_periodo' => $this->indicadorSel->id_periodo,
                ]);
            }
            $msg = 'Metas agregadas exitosamente';
        }
        $this->ResetUI();
        $this->emit('goal-added', $msg);
    }

    //Escuchar los eventos que llegan de javascript
    protected $listeners = [
        'deleteRow' => 'Destroy'
    ];
    //Mètodo para eliminar
    public function Destroy($indicator)
    {
        DB::table('goals')->where('id_indicador', $indicator)->delete();
        $this->ResetUI();
        $this->emit('goal-deleted', 'Meta eliminada');
    }

    public function deshabilitarAno()
    {
        MetaPeriodoHabilitado::where('ano', $this->yearSel)->update([
            'fecha_inactivacion' => date('Y-m-d'),
            'estado' => 'I',
            'id_usuario_inactivacion' => $this->usuarioAct->id,
            'obs_inactivacion' => '',
        ]);
        $this->emit('msg-ok', 'Se habilitó el año ' . $this->yearSel . ' exitosamente');
    }

    public function habilitarAno()
    {
        $estadoPeriodoActual = MetaPeriodoHabilitado::where('ano', $this->yearSel)->first();
        if (is_null($estadoPeriodoActual)) {
            MetaPeriodoHabilitado::create(
                [
                    'ano' => $this->yearSel,
                    'estado' => 'A',
                    'fecha_activacion' => date('Y-m-d'),
                    'id_usuario_activacion' => $this->usuarioAct->id,
                    'obs_activacion' => '',
                ]
            );
        } else {
            $estadoPeriodoActual->estado = 'A';
            $estadoPeriodoActual->fecha_activacion = date('Y-m-d');
            $estadoPeriodoActual->id_usuario_activacion = $this->usuarioAct->id;
            $estadoPeriodoActual->save();
        }
        $this->emit('msg-ok', 'Se habilitó el año ' . $this->yearSel . ' exitosamente');
    }

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }
}
