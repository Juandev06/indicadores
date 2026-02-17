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
                <div class="row mb-1">
                    <div class="col-12 col-sm-6 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text fw-bold">Area</span>
                            <select class="form-select" wire:model="area">
                                {!! $areasAll ? '<option value="0">Todas las áreas</option>' : '' !!}
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3 col-md-3">
                        <div class="input-group">
                            <span class="input-group-text fw-bold">Año</span>
                            <select class="form-select" wire:model="yearAct">
                                @for ($year = $yearIni; $year <= date('Y'); $year++)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3 col-md-3">
                        <div class="input-group">
                            <span class="input-group-text fw-bold">Mes</span>
                            <select class="form-select" wire:model="mesAct">
                                <option value="0">Todos</option>
                                @foreach ($meses as $id => $mes)
                                    <option value="{{ $id }}">{{ ucwords($mes) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-sm-12 col-md-6">
                        <select name="categoria" id="categoria" class="form-select" wire:model="categoriaSel"
                            wire:change="$set('subcategoriaSel', '0')">
                            <option value="0">Todas las categorías</option>
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        @if ($categoriaSel != '0')
                            <select name="subcategoria" id="subcategoria" class="form-select"
                                wire:model="subcategoriaSel">
                                <option value="0">Seleccione una opción</option>
                                @foreach ($subcategorias as $subcat)
                                    <option value="{{ $subcat->id }}">{{ $subcat->nombre }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
                <hr>
                <div class="row no-print">
                    <div class="col-12 col-sm-6 mb-2">
                        @include('common.searchbox')
                    </div>
                    @can($permisoEditarModulo)
                        <div class="col-12 col-sm-6 mb-2">
                            <button class="btn btn-dark mr-05" wire:click="reporteExcel">
                                <span class="fa-solid fa-file-excel"></span>
                                Descargar Reporte <span class="small">({{ $values->total() }} registros)</span>
                            </button>
                        </div>
                    @endcan
                </div>
                <div class="row no-print">
                    <div class="col-12 mb-1">
                        <div class="form-check form-check-danger">
                            <input type="checkbox" class="form-check-input" id="cbSoloPendientes" wire:model="soloPendientes">
                            <label class="form-check-label" for="cbSoloPendientes">Mostrar indicaores sin calcular</label>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm">
                            <thead class="table-dark">
                                <tr>
                                    @if ($this->areasAll)
                                        <th class="text-center">Area</th>
                                    @endif
                                    <th class="text-center">Indicador</th>
                                    <th class="text-center">Mes</th>
                                    <th class="text-center">Meta</th>
                                    <th class="text-center">Valor Ind.</th>
                                    <th class="text-center">Tolerancia</th>
                                    <th class="text-center">Cumplimiento</th>
                                    <th class="text-center">Análisis</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($values->count() <= 0)
                                    <tr>
                                        <td colspan="8">No hay registros.</td>
                                    </tr>
                                @endif
                                @foreach ($values as $valor)
                                    @php
                                        if ($valor->meta_vlr == 0) {
                                            $cumpIndicador = $valor->tendencia == 1 ? 0 : ($valor->ind_vlr == 0 ? 1 : 0);
                                        } else {
                                            if ($valor->tendencia == 1) {
                                                $cumpIndicador = $valor->ind_vlr / $valor->meta_vlr;
                                            } else {
                                                $cumpIndicador = $valor->ind_vlr <= $valor->meta_vlr ? 1 : $valor->meta_vlr / $valor->ind_vlr;
                                            }
                                        }
                                        $cumpIndicadorPrc = (1 - $cumpIndicador) * 100;
                                        $cumpIndicadorDesc = $cumpIndicadorPrc > $valor->tolerancia ? 'N' : ($cumpIndicadorPrc <= $valor->tolerancia && $cumpIndicadorPrc > 0 ? 'T' : 'S');
                                    @endphp
                                    <tr>
                                        @if ($this->areasAll)
                                            <td>{{ $valor->area_name }}</td>
                                        @endif
                                        <td>{{ $valor->ind_name }} ({{ $valor->ind_id }})</td>
                                        <td>{{ $meses[$valor->mes] }}</td>
                                        <td>{{ $valor->meta_vlr }}</td>
                                        <td>{{ round($valor->ind_vlr, 4) }}{{ $valor->tipo_ind == 2 ? '%' : '' }}</td>
                                        <td>{{ $valor->tolerancia }}%</td>
                                        <td>
                                            <div class="d-flex flex-row flex-nowrap justify-content-end">
                                                <span class="mr-05">
                                                    {{ round($cumpIndicador * 100, 2) . '%' }}
                                                </span>
                                                @if ($cumpIndicadorDesc == 'N')
                                                    <span class="badge bg-danger" title="No Cumplió">
                                                        <span class="ico-cancel"></span>
                                                    </span>
                                                @elseif ($cumpIndicadorDesc == 'T')
                                                    <span class="badge bg-warning" title="Tolerancia">
                                                        <span class="ico-warning"></span>
                                                    </span>
                                                @else
                                                    <span class="badge bg-success" title="Cumplió">
                                                        <span class="ico-check"></span>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button
                                                class="btn btn-sm btn-{{ is_null($valor->obs) || $valor->obs == '' ? 'outline-' : '' }}{{ $valor->id_usuario == auth()->user()->id ? 'success' : 'dark' }}"
                                                wire:click="ShowObsAnalisisIndicador({{ $valor->id_vlr }})">
                                                <span
                                                    class="{{ is_null($valor->obs) || $valor->obs == '' ? 'fa-regular fa-comment' : 'fa-solid fa-comment-dots' }}"></span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $values->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('livewire.reportes.analisisIndicadorObs')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.livewire.on('show-obs', msg => {
                $('#analisisIndicadorObs').modal('show');
            });
            window.livewire.on('obs-ok', msg => {
                $('#analisisIndicadorObs').modal('hide');
                noty(msg);
            });
        });
    </script>
</div>
