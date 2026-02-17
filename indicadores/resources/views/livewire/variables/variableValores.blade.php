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
                <div class="show-print text-center mb-1">
                    <span class="fs-24">{{ $pageTitle }}</span> <br>
                    <span class="fs-14">Periodo habilitado: <span
                            class="badge rounded-pill bg-primary">{{ $pageSubtitle }}</span> </span>
                </div>
                @if (!is_null($this->periodoAct))
                    <div class="row">
                        <div class="col-12 col-sm-8 col-md-6 mb-2">
                            @include('common.searchbox')
                        </div>
                    </div>
                @endif

                @if (count($msgResultado) > 0)
                    <div class="row mb-1">
                        <div class="col-12">
                            <h6>
                                @if ($formulasConError)
                                    <span class="badge rounded-pill bg-danger">!</span>
                                @endif
                                Resultado de actualización de indicadores:
                                <button class="btn btn-sm btn-outline-dark" id="btnResultados"
                                    onCLick="mostrarResultado()">
                                    <span id="btnResultadosIco" class="ico-eye"></span>
                                    <span id="btnResultadosTxt">Mostrar</span>
                                </button>
                            </h6>
                            <table class="table table-sm table-bordered d-none" id="tbResultados">
                                <thead>
                                    <tr>
                                        <th>Indicador</th>
                                        <th>Resultado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($msgResultado as $resultado)
                                        <tr class="{{ $resultado['estado'] == 'error' ? 'table-danger' : '' }}">
                                            <td>{{ $resultado['indicador'] }}</td>
                                            <td>{{ $resultado['msg'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                @if (is_null($this->periodoAct))
                    <h6>No puede ingresar datos, no hay periodos habilitados</h6>
                @else
                    <div class="row mb-1">
                        <div class="row-12 d-flex justify-content-between">
                            <div>
                                @can('indicador_analisis_editar')
                                    <button class="btn btn-sm btn-dark mr-05" wire:click="reporteExcel">
                                        <span class="fa-solid fa-file-excel"></span>
                                        Descargar Reporte Registros:
                                        <span class="badge bg-secondary">
                                            {{ $variables->total() }}
                                        </span>
                                    </button>
                                @else
                                    <h6>
                                        Registros: 
                                        <span class="badge bg-secondary">
                                            {{ $variables->total() }}
                                        </span>
                                    </h6>
                                @endcan
                            </div>
                            <div class="form-check form-check-danger">
                                <input type="checkbox" class="form-check-input" id="cbSoloPendientes"
                                    wire:model="soloPendientes">
                                <label class="form-check-label" for="cbSoloPendientes">Ver solo variables
                                    pendientes</label>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">Código</th>
                                    <th class="text-center">Periodicidad</th>
                                    <th class="text-center">Responsable</th>
                                    @if (!$soloPendientes)
                                        <th class="text-center">Valor</th>
                                        <th class="text-center">Fecha</th>
                                        <th class="text-center">Estado</th>
                                    @endif
                                    <th class="text-center no-print">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (is_null($variables))
                                    <tr>
                                        <td colspan="8">No hay registros.</td>
                                    </tr>
                                @else
                                    @foreach ($variables as $variable)
                                        @php
                                            $regVariableAct = $valoresVariables->where('id_variable', $variable->id)->first();
                                            // valor y fecha del registro de la variable actual en el periodo activo
                                            $vlrRegVariableAct = is_null($regVariableAct) ? null : $regVariableAct->valor;
                                            if ($vlrRegVariableAct) {
                                                if ($variable->tipo == 'N') {
                                                    // número a string
                                                    $strVlr = strval($vlrRegVariableAct);
                                                    list($vlrInteger, $vlrDecimal) = explode('.', $strVlr);
                                                    $strDecimal = $vlrDecimal == '0000' ? '' : ',' . rtrim($vlrDecimal, '0'); 
                                                    $vlrRegVariableAct = number_format($vlrInteger, 0, ',', '.') . $strDecimal;
                                                } else {
                                                    $vlrRegVariableAct = round($vlrRegVariableAct, 2) . '%';
                                                }
                                            }
                                            $fechaRegVariableAct = is_null($regVariableAct) ? null : date('Y-m-d', strtotime($regVariableAct->created_at));
                                        @endphp
                                        <tr>
                                            <td>{{ $variable->nombre }}</td>
                                            <td>{{ $variable->id }} </td>
                                            <td>{{ $variable->periodo }}</td>
                                            <td>{{ $variable->responsable }}</td>
                                            @if (!$soloPendientes)
                                                <td class="text-end">{{ $vlrRegVariableAct }}</td>
                                                <td class="text-end">{{ $fechaRegVariableAct }}</td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-{{ is_null($fechaRegVariableAct) ? 'danger' : 'success' }}">
                                                        {{ is_null($fechaRegVariableAct) ? 'Pendiente' : 'Guardado' }}
                                                    </span>
                                                </td>
                                            @endif
                                            <td class="no-print">
                                                <div
                                                    class="d-flex flex-row flex-nowrap align-items-start justify-content-between">
                                                    @can($permisoEditarModulo)
                                                        @if (is_null($fechaRegVariableAct))
                                                            <a href="javascript:void(0)"
                                                                wire:click="Edit({{ $variable->id }}, 'store')"
                                                                class="btn btn-dark mtmobile btn-sm mr-05" title="Guardar">
                                                                <i class="ico-save"></i>
                                                            </a>
                                                        @else
                                                            <a href="javascript:void(0)"
                                                                wire:click="Edit({{ $variable->id }}, 'edit')"
                                                                class="btn btn-dark mtmobile btn-sm mr-05" title="Editar">
                                                                <i class="ico-edit"></i>
                                                            </a>
                                                        @endif
                                                    @endcan
                                                    <a href="javascript:void(0)"
                                                        wire:click="ListarIndicadores('{{ $variable->id }}')"
                                                        class="btn btn-dark btn-sm mr-05" title="Listar Indicadores">
                                                        <i class="ico-chart-line"></i>
                                                    </a>
                                                    @can($permisoEditarModulo)
                                                        <a href="javascript:void(0)"
                                                            onClick="eliminar('{{ $variable->id }}')"
                                                            class="btn btn-danger btn-sm " title="Eliminar">
                                                            <i class="ico-trash"></i>
                                                        </a>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        @if (!is_null($variables))
                            {{ $variables->links() }}
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
    @include('livewire.variables.listarIndicadores')
    @include('livewire.variables.formVariableValores')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.livewire.on('valor-ok', msg => {
                $('#theModalValue').modal('hide');
                noty(msg);
            });
            window.livewire.on('show-modal', msg => {
                $('#theModalValue').modal('show');
            });
            window.livewire.on('msg-ok', msg => {
                noty(msg);
            });
            window.livewire.on('lista-indicadores-rel', msg => {
                $('#listarIndicadores').modal('show');
            });
        })

        function eliminar(id) {
            new swal({
                title: '¿Está seguro de eliminar el valor de la variable?',
                icon: 'error',
                html: '<h5>También se eliminarán los resultados del periodo actual de los indicadores calculados que contengan la variable en sus fórmulas</h5><span style="color:#f00">Esta acción no se puede deshacer</span>',
                showCancelButton: true,
                cancelButtonText: 'Cerrar',
                cancelButtonColor: '#3b3f5c',
                confirmButtonText: 'Eliminar',
                confirmButtonColor: '#ff0000'
            }).then(function(result) {
                if (result.value) {
                    window.livewire.emit('deleteRow', id);
                    swal.close();
                };
            });
        }

        function mostrarResultado() {
            if (document.getElementById('btnResultadosTxt').innerHTML == 'Mostrar') {
                document.getElementById('btnResultadosIco').classList.remove('ico-eye')
                document.getElementById('btnResultadosIco').classList.add('ico-eye-off')
                document.getElementById('btnResultadosTxt').innerHTML = 'Ocultar';
                document.getElementById('tbResultados').classList.remove('d-none');
            } else {
                document.getElementById('btnResultadosIco').classList.remove('ico-eye-off')
                document.getElementById('btnResultadosIco').classList.add('ico-eye')
                document.getElementById('btnResultadosTxt').innerHTML = 'Mostrar';
                document.getElementById('tbResultados').classList.add('d-none');
            }
        }
    </script>
</div>
