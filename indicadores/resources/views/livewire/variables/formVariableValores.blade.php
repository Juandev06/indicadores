@component('common.modal')
    @slot('modalId') theModalValue @endslot
    @slot('modalTipo') {{ $tipoIngreso }} @endslot
    @slot('modalTitulo') Ingresar Valor @endslot

    @slot('contenido')
        <div class="row">
            <div class="col-sm-12 mb-1">
                <div class="form-group">
                    <h4>Variable: <br>
                    {{ $nombreVariableAct }}
                    </h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="input-group mb-2">
                    <span class="input-group-text">Valor:</span>
                    <input type="text" wire:model.lazy="valor" class="form-control" placeholder="Valor *">
                    @if($tipoValor == 'P')
                        <span class="input-group-text">%</span>
                    @endif
                </div>
                @error('valor') <span class="text-danger er">{{ $message }}</span> @enderror
            </div>
        </div>
        @if($fechaValor != '')
        <div class="row">
            <div class="col-12">
                <span class="small">
                    Última actualización del valor: <b>{{ date('Y-m-d h:i A', strtotime($fechaValor)) }}</b>
                </span>
            </div>
        </div>
        @endif
        </div>
        <div id="load_screen_form" wire:loading.delay.longest style="visibility: hidden !important;">
            <div class="loader">
                <div class="loader-content">
                    <div class="spinner-grow align-self-center"></div> Actualizando Formulas..
                </div>
            </div>
        </div>
        <hr>
    @endslot

@endcomponent
