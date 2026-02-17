<div>
    @include('layouts.theme.header')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            @component('common.contentHeaderRow')
                @slot('ComponentName')
                    {{ $ComponentName }}
                @endslot
                @slot('PageTitle')
                    {{ $PageTitle }}
                @endslot
            @endcomponent
            <div class="content-body card p-1">
                <div class="row no-print mb-1">
                    <div class="col-6">
                        @include('common.searchbox')
                    </div>
                    @can($permisoEditarModulo)
                        <div class="col-6">
                            <button id="myBtn" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#theModal">
                                <i class="ico-plus-circle"></i>
                                Agregar
                            </button>
                        </div>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Estado</th>
                                @can($permisoEditarModulo)
                                    <th class="text-center no-print">Acciones</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @if ($areas->count() <= 0)
                                <tr>
                                    <td colspan="3">No hay registros.</td>
                                </tr>
                            @endif
                            @php $i = 0; @endphp
                            @foreach ($areas as $area)
                                <tr>
                                    <td>{{ $area->name }}</td>
                                    <td class="text-center">
                                        <div class="td-content">
                                            <span class="badge bg-{{ $area->status == 'A' ? 'success' : 'danger' }}">
                                                {{ $area->status == 'A' ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </div>
                                    </td>
                                    @can($permisoEditarModulo)
                                        <td class="text-center no-print">
                                            <a href="javascript:void(0)" wire:click="Edit({{ $area->id }})"
                                                class="btn btn-dark btn-sm" title="Editar">
                                                <i class="ico-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)"
                                                onClick="Confirm('{{ $area->id }}', '{$area->$users->count()}')"
                                                class="btn btn-danger btn-sm" title="Eliminar">
                                                <i class="ico-trash"></i>
                                            </a>
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $areas->links() }}
                </div>
            </div>
        </div>
    </div>

    @include('livewire.config.area.form')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            //Escuha si un area fue agregada
            window.livewire.on('area-added', msg => {
                $('#theModal').modal('hide');
                noty(msg);
            });
            //Escuha si un area fue actualizada
            window.livewire.on('area-updated', msg => {
                $('#theModal').modal('hide');
                noty(msg);
            });
            //Escucha si un area fue eliminada
            window.livewire.on('area-deleted', msg => {
                noty(msg);
            });
            //Escucha si es necesario ocultar el modal
            window.livewire.on('hidde-modal', msg => {
                $('#theModal').modal('hide');
            });
            //Escucha si es necesario mostar el modal.
            window.livewire.on('show-modal', msg => {
                $('#theModal').modal('show');
            });
            //Escuha si un quiere limpiar los mensajes de validaciones en el modal
            $('#theModal').on('hidden.bs.modal', function(e) {
                //
            });

            window.livewire.on('area-variables', msg => {
                setError(msg);
            });

            window.livewire.on('area-users', msg => {
                setError(msg);
            });

            window.livewire.on('area-indicators', msg => {
                setError(msg);
            });
        })

        //Funcion para confirmar la eliminacion del registro emitiendo el id a eliminar.
        function Confirm(id, users) {

            if (users > 0) {
                new swal('¡No se puede eliminar el área porque tiene asociado usuarios!')
            }

            new swal({
                title: '¿Está seguro que desea eliminar el área?',
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
