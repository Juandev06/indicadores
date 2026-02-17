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
                <div class="row mb-1">
                    <div class="col-6">
                        @include('common.searchbox')
                    </div>
                    <div class="col-6">
                        <button id="myBtn" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#theModal">
                            <i class="ico-plus-circle"></i>
                            Agregar
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Perfil</th>
                                <th class="text-center">Area</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($data->count() <= 0)
                                <tr>
                                    <td colspan="7">
                                        <h6 class="text-center">No hay registros.</h6>
                                    </td>
                                </tr>
                            @endif
                            @foreach ($data as $r)
                                <tr>
                                    <td> {{ $r->name }} {{ $r->lastName }} </td>
                                    <td> {{ $r->profile }} </td>
                                    <td> {{ $r->area }} </td>
                                    <td class="text-center">
                                        <span
                                            class=" {{ $r->status == 'ACTIVO' ? 'badge bg-success' : 'badge bg-danger' }}">
                                            {{ $r->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-row flex-nowrap align-items-start">
                                            <a href="javascript:void(0)" wire:click="Edit({{ $r->id }})"
                                                class="btn btn-dark btn-sm mr-05" title="Editar">
                                                <i class="ico-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" onClick="Confirm('{{ $r->id }}')"
                                                class="btn btn-danger btn-sm" title="Eliminar">
                                                <i class="ico-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>

    @include('livewire.users.form')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            //Escuha si un type fue agregada
            window.livewire.on('user-added', msg => {
                $('#theModal').modal('hide');
                noty(msg);
            });
            //Escuha si un type fue actualizada
            window.livewire.on('user-updated', msg => {
                $('#theModal').modal('hide');
                noty(msg);
            });
            //Escucha si un user fue eliminada
            window.livewire.on('user-deleted', msg => {
                noty(msg);
            });
            //Escucha si es necesario ocultar el modal
            window.livewire.on('hidde-modal', msg => {
                $('#theModal').modal('hide');
            })
            //userucha si es necesario mostar el modal.
            window.livewire.on('show-modal', msg => {
                $('#theModal').modal('show');
            })
            window.livewire.on('user-indicators', msg => {
                setError(msg);
            });
            window.livewire.on('user-variables', msg => {
                setError(msg);
            });

        })

        //Funcion para confirmar la eliminacion del registro emitiendo el id a eliminar.
        function Confirm(id, ) {

            new swal({
                title: '¿Está seguro que desea eliminar el usuario?',
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
