@component('common.modal')
    @slot('modalTipo') {{ $yearSelEstado == 'A' ? 'CrearOEditar' : 'show' }} @endslot
    @slot('modalTitulo') 
        Metas para el indicador: {{ is_null($indicadorSel->first()) ? '' : $indicadorSel->nombre }} 
    @endslot
    @slot('modalOpc') modal-lg @endslot

    @slot('contenido')
        <div class="alert alert-warning alert-dismissible fade show p-1" role="alert">
            <span class="ico-info"></span>
            <span class="text-black">
                El indicador es de tipo <strong>{{ is_null($indicadorSel->first()) ? '' : 
                ($indicadorSel->tipo == 'P' ? 'Porcentaje' : 'Numérico') }}.</strong> 
                Ingrese el valor correspondiente.
            </span>
            <button type="button" class="btn-close text-black" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div class="row">
        @foreach ($valoresMeta as $idMeta => $valorMeta)
            <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                <div class="input-group mb-1">
                    <label class="input-group-text fw-bolder">{{ $valorMeta['periodo_det'] }}</label>
                    @if ($yearSelEstado == 'A')
                    <input type="number" class="form-control" wire:model.lazy="valoresMeta.{{ $idMeta }}.valor">
                    @else
                    <label class="form-control">{{ $valoresMeta[$idMeta]['valor'] }}</label>
                    @endif
                    <span class="input-group-text fw-bold">
                        {{ is_null($indicadorSel->first()) ? '' : ($indicadorSel->tipo == 'P' ? '%' : '') }}
                    </span>
                    @error('valoresMeta.{{ $idMeta }}.valor')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        @endforeach
        </div>
        <hr>
    @endslot

@endcomponent
