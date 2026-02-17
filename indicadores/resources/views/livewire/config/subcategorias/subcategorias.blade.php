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
                                <th class="text-center">Categoría</th>
                                <th class="text-center">SubCategoría</th>
                                <th class="text-center">Estado</th>
                                @can($permisoEditarModulo)
                                    <th class="text-center no-print">Acciones</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @if ($subcategorias->count() <= 0)
                                <tr>
                                    <td colspan="4">No hay registros.</td>
                                <tr>
                            @endif
                            @foreach ($subcategorias as $subcategoria)
                                <tr>
                                    <td>{{ $subcategoria->categoria }}</td>
                                    <td>{{ $subcategoria->nombre }}</td>
                                    <td class="text-center">
                                        <div class="td-content">
                                            <span
                                                class="badge bg-{{ $subcategoria->estado == 'A' ? 'success' : 'danger' }}">
                                                {{ $subcategoria->estado == 'A' ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </div>
                                    </td>
                                    @can($permisoEditarModulo)
                                        <td class="text-center no-print">
                                            <a href="javascript:void(0)" wire:click="Edit({{ $subcategoria->id }})"
                                                class="btn btn-dark btn-sm" title="Editar">
                                                <i class="ico-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-danger btn-sm"
                                                onClick="Confirm('{{ $subcategoria->id }}')" title="Eliminar">
                                                <i class="ico-trash"></i>
                                            </a>
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $subcategorias->links() }}
                </div>
            </div>
        </div>
    </div>

    @include('livewire.config.subcategorias.form')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.livewire.on('subcategory-added', msg => {
                $('#theModal').modal('hide');
                noty(msg);
            });
            //Escucha si es necesario mostar el modal.
            window.livewire.on('show-modal', msg => {
                $('#theModal').modal('show');
            });
            //Escuha si un area fue actualizada
            window.livewire.on('subcategory-updated', msg => {
                $('#theModal').modal('hide');
                noty(msg);
            });
            window.livewire.on('subcategory-deleted', msg => {
                noty(msg);
            });
            window.livewire.on('subcat-indicators', msg => {
                setError(msg);
            });

        })

        //Funcion para confirmar la eliminacion del registro emitiendo el id a eliminar.
        function Confirm(id) {
            new swal({
                title: '¿Está seguro que desea eliminar la subcategoría?',
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
