<div>
    @include('layouts.theme.header')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            @component('common.contentHeader')
                @slot('pageTitle') {{ $pageTitle }} @endslot
            @endcomponent
            <div class="content-body card p-1">
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Año</span>
                            <select class="form-select" wire:model="yearSel">
                                @for ($year = $yearIni; $year <= date('Y'); $year++)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="alert alert-{{ $yearSelEstado == 'A' ? 'success' : 'dark' }} text-center">
                            <span class="fa-solid fa-circle-{{ $yearSelEstado == 'I' ? 'xmark' : 'check' }} mr-05"></span>
                            <strong>Año {{ $yearSelEstado == 'A' ? '' : 'des' }}habilitado</strong>
                        </div>
                    </div>
                    @if ($permisoHabilitarMeta)
                    <div class="col-sm-12 col-md-8">
                        @if ($yearSelEstado == 'A')
                            <a href="#" class="btn btn-primary" wire:click="deshabilitarAno">
                                <span class="fa-solid fa-ban mr-05"></span>
                                Deshabilitar Año
                            </a>
                        @else
                            <a href="#" class="btn btn-dark" wire:click="habilitarAno">
                                <span class="fa-regular fa-square-check mr-05"></span>
                                Habilitar Año
                            </a>
                        @endif
                    </div>
                    @endif
                </div>
                <hr>
                <div class="row no-print">
                    <div class="col-12 col-md-8 d-flex justify-content-start mb-2">
                        @include('common.searchbox')
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 d-none">
                        <h6>Cantidad de registros: {{ $indicadores->total() }}</h6>
                    </div>
                </div>
                <div class="table-responsive">
                    <h6>Registros: 
                        <div class="badge bg-secondary">
                            {{ $indicadores->total() }}
                        </div>
                    </h6>
                    <table class="table table-striped table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">Indicador</th>
                                <th class="text-center">Periodicidad</th>
                                <th class="text-center">Usuario</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($indicadores->count() == 0)
                                <tr>
                                    <td colspan="5">
                                        <h6 class="text-center">No hay registros.</h6>
                                    </td>
                                </tr>
                            @endif
                            @foreach ($indicadores as $indicador)
                                <tr class="{{ $permisoEditarModulo && $yearSelEstado == 'A' && $indicador->metas_cant == 0 ? 'table-danger' : '' }}">
                                    <td>{{ $indicador->nombre }}</td>
                                    <td>{{ $indicador->periodo }}</td>
                                    <td>{{ $indicador->usuario }}</td>
                                    <td class="text-center">
                                        <div class="d-flex flex-row flex-nowrap align-items-start">
                                            <a href="javascript:void(0)"
                                                wire:click="VerCrearOEditar({{ $indicador->id_indicador }})"
                                                class="btn btn-dark btn-sm mr-05"
                                                @if ($permisoEditarModulo && $yearSelEstado == 'A')
                                                    title="{{ $indicador->metas_cant == 0 ? 'Adicionar' : 'Editar' }}">
                                                    <i class="fa-solid fa-{{ $indicador->metas_cant == 0 ? 'plus' : 'edit'}}"></i>
                                                @else
                                                    title="Detalles">
                                                    <i class="fa-regular fa-rectangle-list"></i>
                                                @endif
                                            </a>
                                            @if ($permisoEditarModulo && $yearSelEstado == 'A')
                                            <a href="javascript:void(0)"
                                                onClick="Confirm('{{ $indicador->id_indicador }}')"
                                                class="btn btn-danger btn-sm" title="Eliminar">
                                                <i class="ico-trash"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if ($indicadores != null)
                        {{ $indicadores->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('livewire.metas.form')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        window.livewire.on('goal-added', msg => {
            $('#theModal').modal('hide');
            noty(msg);
        });
        window.livewire.on('goal-updated', msg => {
            $('#theModal').modal('hide');
            noty(msg);
        });
        window.livewire.on('goal-deleted', msg => {
            noty(msg);
        });
        window.livewire.on('goal-indicator', msg => {
            setError(msg);
        });
        window.livewire.on('hidde-modal', msg => {
            $('#theModal').modal('hide');
        });
        window.livewire.on('show-modal', msg => {
            $('#theModal').modal('show');
        });
        window.livewire.on('msg-ok', msg => {
            noty(msg);
        });
    })

    // confirmar la eliminacion del registro
    function Confirm(id) {
        new swal({
            title: 'Cuidado',
            html: '¿Está seguro que desea eliminar las metas para el indicador?<br> Esta acción no se puede deshacer',
            type: 'warning',
            showCancelButton: true,
            icon: 'error',
            cancelButtonText: 'Cerrar',
            cancelButtonColor: '#3b3f5c',
            confirmButtonText: 'Eliminar',
            confirmButtonColor: '#ea5455'
        }).then(function(result) {
            if (result.value) {
                window.livewire.emit('deleteRow', id);
                swal.close();
            }
        })
    }
</script>
</div>