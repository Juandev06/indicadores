@component('common.modal')
    @slot('modalTipo') show @endslot
    @slot('modalOpc') modal-fullscreen @endslot
    @slot('modalTitulo')  @endslot
    @slot('modalId') buscarVariables @endslot
    @slot('modalCerrar') ResetBuscarVar @endslot

    @slot('contenido')
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="input-group input-group-merge">
                <input type="text" wire:model.defer="filtroVar" placeholder="buscar" class="form-control"
                     wire:keydown.enter="buscarVariables">
                @if($filtroVar != '')
                    <span class="input-group-text cursor-pointer text-secondary btn-search" wire:click="limpiarFiltroVariables" title="Limpiar Filtro">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </span>
                @endif
                <button class="btn btn-relief-secondary d-flex flex-nowrap" wire:click="buscarVariables">
                    <i class="fa-solid fa-search mr-05"></i>
                    <span class="d-none d-sm-block">Filtrar</span>
                </button>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <select id="listVariables" class="form-select" wire:model="periodoVar" wire:change="buscarVariables">
                @foreach ($periodosVar as $periodoVar)
                    <option value="{{ $periodoVar->id }}">{{ $periodoVar->nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Cod.</th>
                    <th>Nombre</th>
                    <th>Área</th>
                    <th>Responsable</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($formulaVars as $var)
                <tr wire:key="{{ $var->id }}">
                    <td>{{ $var->id }}</td>
                    <td>{{ $var->nombre }}</td>
                    <td>{{ $var->area }}</td>
                    <td>{{ $var->responsable }}</td>
                    <td>
                        <button type="button" class="btn btn-dark btn-sm" idVar="{{ $var->id }}" 
                            nombreVar="{{ $var->nombre }}" onClick="InsertarVariable()">
                            Adicionar
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endslot

@endcomponent
