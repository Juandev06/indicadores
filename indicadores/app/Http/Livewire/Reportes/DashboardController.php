<?php

namespace App\Http\Livewire\Reportes;

use App\Models\Aux\PeriodoDets;
use App\Models\Config\Area;
use App\Models\Indicadores\Indicadores;
use App\Models\Dashboard;
use App\Models\Indicadores\IndicadorValores;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DashboardController extends Component
{
    use WithPagination;
    public
        $permisoModulo, // nombre del permiso de módulo actual
        $usuarioAct, // usuario actual (con sesión activa)
        $areaUsuario, // área del usuario actual
        $ComponentName, $indicadores, $itemsDashboard, 
        $idIndicator1, $tipoItem1, $dataItem1, $idIndicator2, $tipoItem2, $dataItem2, $idIndicator3, $tipoItem3, $dataItem3,
        $idIndicator4, $tipoItem4, $dataItem4, $idIndicator5, $tipoItem5, $dataItem5, $idIndicator6, $tipoItem6, $dataItem6,
        $yearAct, $yearIni, $mesAct, $showVariables;

    public function mount($ano = 0)
    {
        $this->permisoModulo = 'dashboard';
        $this->ComponentName = 'Dashboard';
        $this->usuarioAct = Auth::user();
        $this->areaUsuario = Area::find($this->usuarioAct->id_area)->name;
        $this->yearAct = $ano == 0 ? date('Y') : $ano; //TODO: guardar en base de datos
        $this->yearIni = env('YEAR_INI');

        $this->mesAct = date('n'); //TODO: guardar en base de datos
        $this->showVariables = false;
        // listar indicadores activos
        $this->indicadores = Indicadores::where('estado', 'A')
            ->orderBy('nombre')
            ->get();
        // inicializar variables idIndicador y tipoItem
        for ($i = 1; $i <= 6; $i++) {
            $indicadorId = 'idIndicator' . $i;
            $this->{$indicadorId} = 0;
            $tipoChart = 'tipoItem' . $i;
            $this->{$tipoChart} = 'line';
            $dataItem = 'dataItem' . $i;
            $this->{$dataItem} = '';
        }
        // obtener los items del dashboard para el usuario actual
        $this->itemsDashboard = Dashboard::where('id_usuario', Auth::user()->id)
            ->orderBy('order')
            ->get();
        // asignar valores en BD a idIndicador y tipoItem
        foreach ($this->itemsDashboard as $itemDashboard) {
            $indicadorId = 'idIndicator' . $itemDashboard->order;
            $this->{$indicadorId} = $itemDashboard->id_indicator;
            $tipoChart = 'tipoItem' . $itemDashboard->order;
            $this->{$tipoChart} = $itemDashboard->chart_type;
            $dataItem = 'dataItem' . $itemDashboard->order;
            // indicador:
            $indicador = Indicadores::find($this->{$indicadorId});
            // periodos:
            $chartPeriodos = [];
            $periodos = PeriodoDets::where('id_periodo', $indicador->id_periodo)
                ->where('id_calendario', $indicador->id_calendario)
                ->get();
            $chartData = [];
            $chartDataDB = IndicadorValores::where('id_indicador', $this->{$indicadorId})
                ->where('ano', $this->yearAct)
                ->get();
            $colores = [];
            $coloresConst = config('constantes.chartColors');
            $contador = 0;
            foreach ($periodos as $periodo) {
                $colores[] = ($this->{$tipoChart} == 'line' || $this->{$tipoChart} == 'bar') ? '#28dac6' : $coloresConst[$contador];
                $chartPeriodos[] = $periodo->nombre;
                $chartDataDBAct = $chartDataDB->where('mes', $periodo->mes)->first();
                $chartData[] = is_null($chartDataDBAct) ? 0 : round($chartDataDBAct->valor, 4);
                $contador = $contador < 19 ? $contador + 1 : 0;
            }
            $this->{$dataItem} = [
                'labels' => $chartPeriodos,
                'datasets' => [
                    [
                        'data' => $chartData,
                        'label' => $indicador->nombre,
                        'backgroundColor' => $colores,
                        'borderColor' => $colores,
                    ]
                ],
            ];
        }
    }

    public function render()
    {
        // validar el ingreso del usuario al módulo actual
        if (! $this->usuarioAct->can($this->permisoModulo)) {
            return abort('403');
        }
        return view('livewire.reportes.dashboard')
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function ResetUI()
    {
        $this->resetPage();
    }

    // create o update graficos del dashboard
    public function Update($idOrdenDashboard)
    {
        $idUsuario = $this->usuarioAct->id;
        $idIndicatorAct = 'idIndicator' . $idOrdenDashboard;
        $chartTypeAct = 'tipoItem' . $idOrdenDashboard;
        $dashboardDets = Dashboard::where('id_usuario', $idUsuario)
            ->where('order', $idOrdenDashboard)
            ->first();
        if ($this->{$idIndicatorAct} > 0) {
            if (is_null($dashboardDets)) {
                Dashboard::create([
                    'order' => $idOrdenDashboard,
                    'id_indicator' => $this->{$idIndicatorAct},
                    'id_usuario' => $idUsuario,
                    'chart_type' => $this->{$chartTypeAct},
                    'show_detail' => false,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $dashboardDets->id_indicator = $this->{$idIndicatorAct};
                $dashboardDets->chart_type = $this->{$chartTypeAct};
                $dashboardDets->save();
            }
        }
        return redirect()->route('dashboard', [
            'ano' => $this->yearAct
        ]);
    }

    public function actualizarAno()
    {
        return redirect()->route('dashboard', [
            'ano' => $this->yearAct
        ]);
    }
}
