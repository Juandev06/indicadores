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
            @endcomponent
            <div class="content-body card p-1">
                <div class="row">
                    <div class="col-6">
                        @include('common.searchbox')
                    </div>
                    <div class="col-6">
                        <div class="input-group">
                            <span class="input-group-text fw-bold">Filtrar Rol:</span>
                            <select class="form-select" wire:model="rolSel">
                                <option value="0">Todos</option>
                                @foreach ($rolesDB as $rol)
                                    <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">Nombre Permiso</th>
                            @foreach ($roles as $rol)
                                <th class="text-center">{{ $rol->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permisos as $permiso)
                            <tr>
                                <td>
                                    <span class="mr-05">
                                        {{ strToUpper($permiso->name) }}
                                    </span>
                                    <span class="badge" style="cursor: pointer;" data-bs-toggle="tooltip"
                                        data-bs-placement="top" data-bs-original-title="{{ $permiso->obs }}">
                                        <span class="fa-solid fa-lightbulb text-warning"></span>
                                    </span>
                                </td>
                                @foreach ($roles as $rol)
                                    <td class="text-center">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="rolesPermisos.r{{ $rol->id }}p{{ $permiso->id }}"
                                                wire:click="UpdatePermiso({{ $rol->id }},{{ $permiso->id }})">
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.livewire.on('msg-ok', msg => {
                noty(msg);
            });
            window.livewire.on('msg-error', msg => {
                notyError(msg);
            });
        })
    </script>

</div>
