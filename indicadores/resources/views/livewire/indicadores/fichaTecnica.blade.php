<div class="modal fade" tabindex="-1" id="fichaTecnica" aria-hidden="true" wire:ignore.self wire:ignore-self
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                    aria-label="Close" wire:click.prevent="ResetUI()"></button>
            </div>
            <div class="modal-body px-sm-1 mx-50 pb-2">
                <h2 class="text-center mb-1" id="modalTitle">
                    {{ 
                        (auth()->user()->can($permisoEditarModulo) && is_null($indicadorAct['ficha_tec_archivo'])) ? 
                            'Cargar' : '' 
                    }} Ficha Técnica
                </h2>
                <hr>
                <h4><b>Indicador:</b> {{ $idIndicador }} - {{ $indicadorAct['nombre'] }}</h4>
                <hr>
                @if(!is_null($indicadorAct['ficha_tec_archivo']))
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary" wire:click.prevent="DescargarArchivo()">
                            <span class="fa-solid fa-download"></span> Descargar Ficha Técnica
                        </button>
                    </div>
                </div>
                <hr>
                @endif
                @can($permisoEditarModulo)
                <div class="row">
                    <div class="col-sm-12">
                        @if (Session::get('errorAnexo'))
                            <div class="alert alert-danger">
                                {{ Session::get('errorAnexo') }}
                            </div>
                        @endif
                        @if (Session::get('sucess'))
                            <div class="alert alert-primary">
                                {{ Session::get('sucess') }}
                            </div>
                        @endif
                        <form wire:submit.prevent="UploadFile">
                            <div class="row">
                                <div class="col-sm-12 mb-1">
                                    <label for="">Seleccionar archivo (solo extensiones {{ env('EXTENSION_ARCHIVOS') }}):</label> <br>
                                    <input type="file" wire:model="archivoDoc">
                                    <br>
                                    @error('archivoDoc') 
                                        <span class="error">{{ $message }}</span> 
                                    @enderror
                                </div>
                                @if(0) {{-- $docUploading --}}
                                <div class="alert alert-primary mb-1">
                                    <div class="alert-body">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        <span><strong>Cargando...</strong></span>
                                    </div>
                                </div>
                                @endif
                                <div class="col-12 text-center pb-2">
                                    <button type="submit" class="btn btn-dark close-modal">
                                        <span class="fa-solid fa-{{ is_null($indicadorAct['ficha_tec_archivo']) ? 'upload' : 'repeat' }}"></span>
                                        {{ is_null($indicadorAct['ficha_tec_archivo']) ? 'Cargar' : 'Actualizar' }}
                                    </button>
                                    <button type="button" wire:click.prevent="ResetUI()" class="btn btn-outline-dark"
                                        data-bs-dismiss="modal">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endcan
                @cannot($permisoEditarModulo)
                    <div class="row">
                        @if(is_null($indicadorAct['ficha_tec_archivo']))
                            <h4 class="text-center">No hay ficha técnica cargada para este indicador</h4>
                            <hr>
                        @endif
                        <div class="col-12 text-center pb-2">
                            <button type="button" wire:click.prevent="ResetUI()" class="btn btn-outline-dark"
                                data-bs-dismiss="modal">
                                Cerrar
                            </button>
                        </div>    
                    </div>
                @endcannot
            </div>
        </div>
    </div>
</div>
