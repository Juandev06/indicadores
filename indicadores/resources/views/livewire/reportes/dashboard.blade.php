<div>
    @include('layouts.theme.header')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="app-content">
                @component('common.contentHeader')
                    @slot('pageTitle')
                        {{ $ComponentName }}
                    @endslot
                @endcomponent

                <div class="content-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 col-xl-6 col-xxl-6 col-12">
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold">Año</span>
                                        <select class="form-select" wire:model="yearAct" wire:change="actualizarAno">
                                            @for ($year = $yearIni; $year <= date('Y'); $year++)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endfor
                                        </select>
                                        @if ($showVariables)
                                            <span class="input-group-text fw-bold">Mes</span>
                                            <select class="form-select" wire:model="mesAct">
                                                @foreach (Config::get('constantes.meses') as $id => $mes)
                                                    <option value="{{ $id }}">{{ ucwords($mes) }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-text">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="dshbrdAcumul" value="0">
                                                    <label class="custom-control-label" for="dshbrdAcumul">
                                                        Datos Acumulados</label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6 col-xl-6 col-xxl-6 col-12">
                                    @if ($showVariables)
                                        <div class="input-group-text">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="dshbrdVariables"
                                                    value="0">
                                                <label class="custom-control-label" for="dshbrdVariables">
                                                    Mostrar variables</label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <section id="basic-horizontal-layouts">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                {{-- INDICADOR 1 --}}
                                <div class="card">
                                    <div class="card-header">
                                        <div class="input-group">
                                            <span class="input-group-text">Indicador:</span>
                                            <select class="form-select" wire:model="idIndicator1"
                                                wire:change="Update(1)">
                                                <option value="0">Seleccione</option>
                                                @foreach ($indicadores as $indicador)
                                                    <option value="{{ $indicador->id }}"> {{ $indicador->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text">Tipo de Gráfico:</span>
                                            <select class="form-select" wire:model="tipoItem1" wire:change="Update('1')"
                                                id="tipoItem1">
                                                @foreach (Config::get('constantes.chartTypes') as $id => $tipo)
                                                    <option value="{{ $id }}">{{ ucwords($tipo) }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if ($idIndicator1 > 0)
                                            <canvas id="ind01" class="bar-chart-ex chartjs"
                                                data-height="400"></canvas>
                                        @endif
                                        {{-- Mostrar el detalle de las variables de un indicador
                                    <div class="chart-desc-card">
                                        <hr>
                                        <h4>Datos {{ $acumulado ? ' acumulados a ' : '' }} {{ $per1 }}</h4><br>
                                        <h5><span class="fw-bold">Formula:</span>
                                        <span class="font-small-3">{{ $chart1->formula }}</span></h5>
                                        <hr>
                                        <div class="row g-1">
                                            @foreach ($chart1->variables as $var)
                                            <div class="chart-desc col-6">
                                                <div class="chart-ico-desc rounded-circle me-1 p-1">
                                                    <span class="ico-bullseye"></span>
                                                </div>
                                                <div class="">
                                                    <h4 class="fw-bold">{{ $var->vlr }}</h4>
                                                    <p class="card-text font-small-3 mb-50">{{ $var->name }}</p>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        </div> --}}

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                {{-- INDICADOR 2 --}}
                                <div class="card">
                                    <div class="card-header">
                                        <div class="input-group">
                                            <span class="input-group-text">Indicador:</span>
                                            <select class="form-select" wire:model="idIndicator2"
                                                wire:change="Update(2)">
                                                <option value="0">Seleccione</option>
                                                @foreach ($indicadores as $indicador)
                                                    <option value="{{ $indicador->id }}">{{ $indicador->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text">Tipo de Gráfico:</span>
                                            <select class="form-select" wire:model="tipoItem2" wire:change="Update('2')"
                                                id="tipoItem2">
                                                @foreach (Config::get('constantes.chartTypes') as $id => $tipo)
                                                    <option value="{{ $id }}">{{ ucwords($tipo) }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if ($idIndicator2 > 0)
                                            <canvas id="ind02" class="bar-chart-ex chartjs"
                                                data-height="400"></canvas>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="basic-vertical-layouts">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                {{-- INDICADOR 3 --}}
                                <div class="card">
                                    <div class="card-header">
                                        <div class="input-group">
                                            <span class="input-group-text">Indicador:</span>
                                            <select class="form-select" wire:model="idIndicator3"
                                                wire:change="Update(3)">
                                                <option value="0">Seleccione</option>
                                                @foreach ($indicadores as $indicador)
                                                    <option value="{{ $indicador->id }}">{{ $indicador->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text">Tipo de Gráfico:</span>
                                            <select class="form-select" wire:model="tipoItem3" wire:change="Update(3)"
                                                id="tipoItem3">
                                                @foreach (Config::get('constantes.chartTypes') as $id => $tipo)
                                                    <option value="{{ $id }}">{{ ucwords($tipo) }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if ($idIndicator3 > 0)
                                            <canvas id="ind03" class="bar-chart-ex chartjs"
                                                data-height="400"></canvas>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                {{-- INDICADOR 4 --}}
                                <div class="card">
                                    <div class="card-header">
                                        <div class="input-group">
                                            <span class="input-group-text">Indicador:</span>
                                            <select class="form-select" wire:model="idIndicator4"
                                                wire:change="Update(4)">
                                                <option value="0">Seleccione</option>
                                                @foreach ($indicadores as $indicador)
                                                    <option value="{{ $indicador->id }}">{{ $indicador->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text">Tipo de Gráfico:</span>
                                            <select class="form-select" wire:model="tipoItem4"
                                                wire:change="Update(4)" id="tipoItem4">
                                                @foreach (Config::get('constantes.chartTypes') as $id => $tipo)
                                                    <option value="{{ $id }}">{{ ucwords($tipo) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if ($idIndicator4 > 0)
                                            <canvas id="ind04" class="bar-chart-ex chartjs"
                                                data-height="400"></canvas>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>



@section('jsAdicional')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script>
        var tipoItem1 = '{{ $tipoItem1 }}';
        var tipoItem2 = '{{ $tipoItem2 }}';
        var tipoItem3 = '{{ $tipoItem3 }}';
        var tipoItem4 = '{{ $tipoItem4 }}';
        const dataLabelConf = {
            default: {
                anchor: "end",
                align: "end",
                labels: {
                    value: {
                        color: "#222",
                        backgroundColor: "#ddd",
                    },
                },
            },
            pie: {
                align: 'end',
                formatter: (val) => `${val}%`,
                labels: {
                    value: {
                        color: '#222',
                        backgroundColor: '#ccc'
                    }
                }
            }
        };

        var dataLabelConf1 = (tipoItem1 == 'pie' || tipoItem1 == 'doughnut') ? dataLabelConf.pie : dataLabelConf.default;
        var dataLabelConf2 = (tipoItem2 == 'pie' || tipoItem2 == 'doughnut') ? dataLabelConf.pie : dataLabelConf.default;
        var dataLabelConf3 = (tipoItem3 == 'pie' || tipoItem3 == 'doughnut') ? dataLabelConf.pie : dataLabelConf.default;
        var dataLabelConf4 = (tipoItem4 == 'pie' || tipoItem4 == 'doughnut') ? dataLabelConf.pie : dataLabelConf.default;

        var dataInd01 = @js($dataItem1);
        var dataInd02 = @js($dataItem2);
        var dataInd03 = @js($dataItem3);
        var dataInd04 = @js($dataItem4);
    </script>
    {{-- <script src="{{ asset(mix('js/dashboard.js')) }}"></script> --}}
    <script>
        Chart.register(ChartDataLabels);

        // INDICADOR 01
        var ctx01 = document.getElementById('ind01');
        var ind01 = new Chart(ctx01, {
            type: tipoItem1,
            data: dataInd01,
            options: {
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                responsive: true,
                plugins: {
                    datalabels: dataLabelConf1
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // INDICADOR 02
        var ctx02 = document.getElementById('ind02');
        var ind02 = new Chart(ctx02, {
            type: tipoItem2,
            data: dataInd02,
            options: {
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                responsive: true,
                plugins: {
                    datalabels: dataLabelConf2
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // INDICADOR 03
        var ctx03 = document.getElementById('ind03');
        var ind03 = new Chart(ctx03, {
            type: tipoItem3,
            data: dataInd03,
            options: {
                responsive: true,
                plugins: {
                    datalabels: dataLabelConf3,
                },
            },
        });

        // INDICADOR 04
        var ctx04 = document.getElementById('ind04');
        var ind04 = new Chart(ctx04, {
            type: tipoItem4,
            data: dataInd04,
            options: {
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                responsive: true,
                plugins: {
                    datalabels: dataLabelConf4,
                },
            },
        });

        window.addEventListener('refresh-page', event => {
            window.location.reload(false);
        })
        document.addEventListener('DOMContentLoaded', function() {
            window.livewire.on('update-chart', id => {
                select = document.getElementById('tipoItem' + id);
                tipoChart = select.options[select.selectedIndex].value;
                dataLabelConfAct =
                    (tipoChart == "pie" || tipoChart == "doughnut") ?
                    dataLabelConf.pie :
                    dataLabelConf.default;

                if (id == 1) {
                    ind01.destroy();
                    ind01 = new Chart(ctx01, {
                        type: tipoChart,
                        data: dataInd01,
                        options: {
                            plugins: {
                                datalabels: dataLabelConfAct,
                            },
                        },
                    });
                }
                if (id == 2) {
                    ind02.destroy();
                    ind02 = new Chart(ctx02, {
                        type: tipoChart,
                        data: dataInd02,
                        options: {
                            plugins: {
                                datalabels: dataLabelConfAct,
                            },
                        },
                    });
                }
                if (id == 3) {
                    ind03.destroy();
                    ind03 = new Chart(ctx03, {
                        type: tipoChart,
                        data: dataInd03,
                        options: {
                            plugins: {
                                datalabels: dataLabelConfAct,
                            },
                        },
                    });
                }
                if (id == 4) {
                    ind04.destroy();
                    ind04 = new Chart(ctx04, {
                        type: tipoChart,
                        data: dataInd04,
                        options: {
                            plugins: {
                                datalabels: dataLabelConfAct,
                            },
                        },
                    });
                }
            });
        });
    </script>
@endsection
