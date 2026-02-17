<?php

namespace App\Http\Livewire\Variables;

use App\Models\Config\Area;
use App\Models\Variables\VariablePeriodoHabilitado;

use Illuminate\Support\Facades\Config;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VariableHabilitarPeriodoController extends Component
{
    public  
        $permisoModulo, // nombre del permiso de módulo actual
        $usuarioAct, // usuario actual (con sesión activa)
        $areaUsuario, // área del usuario actual
        $pageTitle, $pageSubtitle, $ComponentName, 

        $periodoAct, // periodo actvo (ver tabla variable_periodo_habilitado)
        $yearAct, // año actual (año habilitado)
        $yearIni, // año inicial (inicio de datos)
        $meses, // listado de meses
        $mesAct, // mes actual (mes habilitado)

        $pagination;

    public function mount()
    {
        $this->permisoModulo = 'variable_habilitar';
        $this->usuarioAct = Auth::user();
        $this->areaUsuario = Area::find($this->usuarioAct->id_area)->name;
        $this->pageTitle = 'Periodo Para Ingresar Variables';
        $this->ComponentName = 'Habilitar ingreso de variables';
        $this->pagination = env('PAGINATION');
        
        $this->periodoAct = VariablePeriodoHabilitado::where('estado', 'A')->first();
        $this->yearAct = is_null($this->periodoAct) ? date('Y') : $this->periodoAct->ano;
        $this->yearIni = env('YEAR_INI');
        $this->meses = Config::get('constantes.meses');
        $this->pageSubtitle = is_null($this->periodoAct) ? 'No hay periodos habilitados' : 
            strtoupper($this->meses[$this->periodoAct->mes]) . ' - '. $this->periodoAct->ano;
    }
    
    public function render()
    {
        // validar el ingreso del usuario al módulo actual
        if (! $this->usuarioAct->can($this->permisoModulo)) {
            return abort('403');
        }
        // obtener el periodo activo
        $this->periodoAct = VariablePeriodoHabilitado::where('estado', 'A')
            ->where('ano', $this->yearAct)
            ->first();
        $this->mesAct = is_null($this->periodoAct) ? null : $this->periodoAct->mes;
        if (!is_null($this->periodoAct)) {
            $this->pageSubtitle = strtoupper($this->meses[$this->periodoAct->mes]) . ' - '. 
                $this->periodoAct->ano;
        }
        //dd($this->periodoAct);
        return view('livewire.variables.habilitarPeriodo')
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function Habilitar($mes)
    {
        // deshabilitar el periodo activo actualmente
        $periodoAct = VariablePeriodoHabilitado::where('estado', 'A')->first();
        if (!is_null($periodoAct)) {
            $periodoAct->estado = 'I';
            $periodoAct->fecha_inactivacion = date('Y-m-d H:i:s');
            $periodoAct->id_usuario_inactivacion = $this->usuarioAct->id;
            $periodoAct->save();
        }
        // crear nuevo registro de periodo activo
        VariablePeriodoHabilitado::create([
            'ano' => $this->yearAct,
            'mes' => $mes,
            'estado' => 'A',
            'fecha_activacion' => date('Y-m-d H:i:s'),
            'id_usuario_activacion' => $this->usuarioAct->id,
        ]);

        $this->emit('msg-ok', 'Periodo habilitado exitosamente');
    }

    public function Deshabilitar($mes)
    {
        // deshabilitar el periodo solicitado
        $periodoAct = VariablePeriodoHabilitado::where('estado', 'A')
            ->where('ano', $this->yearAct)
            ->where('mes', $mes)
            ->first();
        if (!is_null($periodoAct)) {
            $periodoAct->estado = 'I';
            $periodoAct->fecha_inactivacion = date('Y-m-d H:i:s');
            $periodoAct->id_usuario_inactivacion = $this->usuarioAct->id;
            $periodoAct->save();
        }
        $this->pageSubtitle = 'No hay periodos habilitados';
        $this->emit('msg-ok', 'Periodo deshabilitado exitosamente');
    }
}
