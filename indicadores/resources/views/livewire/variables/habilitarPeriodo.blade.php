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
                    Periodo habilitado: <span class="badge rounded-pill bg-primary">{{ $pageSubtitle }}</span>
                @endslot
            @endcomponent
            <div class="content-body card p-1">
                <div class="row mb-2">
                    <div class="col-6">
                        <div class="input-group mb-2">
                            <span class="input-group-text">Año</span>
                            <select class="form-select" wire:model="yearAct">
                                @for ($year = $yearIni; $year <= date('Y'); $year++)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-white text-center">Periodo</th>
                                <th class="text-white text-center">Estado</th>
                                <th class="text-white text-center">Fecha Activación</th>
                                <th class="text-white text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($meses as $idMes => $mes)
                                <tr>
                                    <td>
                                        <h6>{{ $mes }}</h6>
                                    </td>
                                    <td>
                                        <span class="{{ $mesAct == $idMes ? 'badge bg-success' : 'badge bg-danger' }}">
                                            {{ $mesAct == $idMes ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="text-right" style="text-align:right">
                                        @if ($mesAct == $idMes)
                                            {{ $periodoAct->fecha_activacion }}
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button"
                                            class="btn btn-sm btn-{{ $mesAct == $idMes ? '' : 'outline-' }}dark"
                                            wire:click.prevent="{{ $mesAct == $idMes ? 'Deshabilitar(' . $idMes . ')' : 'Habilitar(' . $idMes . ')' }}">
                                            {{ $mesAct == $idMes ? 'Deshabilitar' : 'Habilitar' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // mostrar mensaje de exito
            window.livewire.on('msg-ok', msg => {
                noty(msg);
            });
            // mostrar mensaje de error
            window.livewire.on('msg-error', msg => {
                notyError(msg);
            });
        });
    </script>
</div>
