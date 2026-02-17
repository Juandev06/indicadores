@component('common.modal')
    @slot('modalId') analisisIndicadorObs @endslot
    @slot('modalOpc') modal-lg @endslot
    @slot('modalTipo') 
        {!! (auth()->user()->can($permisoEditarModulo) || auth()->user()->id == $idUsuarioIndicadorAct) ? 
            'ObsAnalisisIndicador()' : 'show' !!} 
    @endslot
    @slot('modalTitulo')
        <div class="d-flex justify-content-center">
            <h2>Análisis del indicador:</h2>
        </div>
    @endslot

    @slot('contenido')
        <div class="row">
            <div class="col-12">
                <h4>
                    <b>Indicador:</b> {{ $nombreIndicadorAct }}
                </h4>
            </div>
        </div>
        <hr>
        <div class="row mb-1">
            <div class="col-12">
                <div class="form-group">
                    @if(auth()->user()->can($permisoEditarModulo) || auth()->user()->id == $idUsuarioIndicadorAct)
                        <label for="obsValorIndicador" class="">Ingrese observaciones al resultado del indicador:</label>
                        <textarea id="obsValorIndicador" wire:model="obsValorIndicador" rows="8" class="form-control"></textarea>
                    @else
                        <h5>Observaciones al resultado del indicador:</h5><br>
                        <p>{{ $obsValorIndicador }}</p>
                    @endif
                </div>
            </div>
        </div>
        <hr>
    @endslot
@endcomponent
