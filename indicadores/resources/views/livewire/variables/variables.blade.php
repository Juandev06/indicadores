<div>
    @include('layouts.theme.header')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            @component('common.contentHeader')
                @slot('pageTitle')
                    {{ $pageTitle }}
                @endslot
                @slot('pageSubtitle')
                    Cantidad de variables: {{ $variables->total() }}
                @endslot
            @endcomponent
            <div class="content-body card p-1">
                <div class="row no-print">
                    <div class="col-12 col-md-8 d-flex justify-content-start mb-2">
                        @include('common.searchbox')
                        @can($permisoEditarModulo)
                            <div>
                                <button id="myBtn" class="btn btn-dark d-flex flex-nowrap" data-bs-toggle="modal"
                                    data-bs-target="#theModal">
                                    <i class="ico-plus-circle"></i>
                                    <span>Agregar</span>
                                </button>
                            </div>
                        @endcan
                    </div>
                </div>
                <div class="show-print text-center mb-1">
                    <span class="fs-24">{{ $pageTitle }}</span> <br>
                    <span class="fs-14">Cantidad de variables: {{ $variables->total() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">Nombre Variable</th>
                                <th class="text-center">Cod</th>
                                <th class="text-center">Area</th>
                                <th class="text-center">Responsable</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Periodicidad</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center no-print">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($variables->count() <= 0)
                                <tr>
                                    <td colspan="7">
                                        <h6 class="text-center">No hay registros.</h6>
                                    </td>
                                </tr>
                            @endif
                            @foreach ($variables as $variable)
                                <tr>
                                    <td>{{ $variable->nombre }}</td>
                                    <td>{{ $variable->id }}</td>
                                    <td>{{ $variable->area }}</td>
                                    <td>{{ $variable->userName }}</td>
                                    <td>{{ $variable->tipo == 'P' ? '%' : 'N°' }}</td>
                                    <td>{{ $variable->periodo }}</td>
                                    <td class="text-center">
                                        <div class="td-content">
                                            <span
                                                class=" {{ $variable->estado == 'A' ? 'badge bg-success' : 'badge bg-danger' }}">
                                                {{ $variable->estado == 'A' ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center no-print">
                                        <div class="d-flex flex-row flex-nowrap align-items-start">
                                            @can($permisoEditarModulo)
                                                <a href="javascript:void(0)" wire:click="Edit({{ $variable->id }})"
                                                    class="btn btn-dark btn-sm mtmobile mr-05" title="Editar">
                                                    <i class="ico-edit"></i>
                                                </a>
                                            @endcan
                                            <a href="javascript:void(0)"
                                                wire:click="ListarIndicadores('{{ $variable->id }}')"
                                                class="btn btn-dark btn-sm mr-05" title="Listar Indicadores">
                                                <i class="ico-chart-line"></i>
                                            </a>
                                            @can($permisoEditarModulo)
                                                <a href="javascript:void(0)" onClick="Confirm('{{ $variable->id }}')"
                                                    class="btn btn-danger btn-sm" title="Eliminar">
                                                    <i class="ico-trash"></i>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $variables->links() }}
                </div>
            </div>
        </div>
    </div>

    @include('livewire.variables.formVariables')
    @include('livewire.variables.listarIndicadores')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.livewire.on('variable-added', msg => {
                $('#theModal').modal('hide');
                noty(msg);
            });
            window.livewire.on('variable-updated', msg => {
                $('#theModal').modal('hide');
                noty(msg);
            });
            window.livewire.on('variable-deleted', msg => {
                noty(msg);
            });
            window.livewire.on('hidde-modal', msg => {
                $('#theModal').modal('hide');
            })
            window.livewire.on('show-modal', msg => {
                $('#theModal').modal('show');
            })
            window.livewire.on('variable-indicators', msg => {
                setError(msg);
            });
            window.livewire.on('error-variableName', msg => {
                setError(msg);
            });
            window.livewire.on('lista-indicadores', msg => {
                $('#listarIndicadores').modal('show');
            });
        })

        //Funcion para confirmar la eliminacion del registro emitiendo el id a eliminar.
        function Confirm(id, ) {
            new swal({
                title: '¿Está seguro que desea eliminar la variable?',
                html: '<h4 style="color:#f00">Esta acción no se puede deshacer</h4>',
                icon: 'error',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                cancelButtonColor: '#3b3f5c',
                confirmButtonText: 'Eliminar',
                confirmButtonColor: '#f00'
            }).then(function(result) {
                if (result.value) {
                    window.livewire.emit('deleteRow', id);
                    swal.close();
                };
            });
        }
    </script>
</div>
