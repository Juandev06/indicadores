<div class="modal fade" tabindex="-1" id="formulaModal" aria-labelledby="addNewCardTitle" aria-hidden="true"
    wire:ignore.self wire:ignore-self data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    wire:click.prevent="ResetUI()"></button>
            </div>
            <div class="modal-body px-sm-1 mx-50 pb-2">
                <form id="modalForm" class="row gy-1 gx-2 mt-25" onsubmit="return false">
                    <div class="row">
                        @if(auth()->user()->can($permisoEditarModulo))
                        <div class="col-sm-12 col-md-6">
                            <h5>Formula:</h5>
                            <textarea class="form-control" id="txtFormula" rows="10" wire:model.lazy="formulaStored"></textarea>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <button type="button" class="btn btn-dark mt-2" wire:click="abrirBuscarVariables">
                                <span class="fa-solid fa-search mr-05"></span>    
                                Buscar Variables
                            </button>
                            <hr>
                            <div class="col-sm-12">
                                <h5>Insertar un operador:</h5>
                            </div>
                            <div class="col-sm-12">
                                <button class="btn btn-outline-dark btn-sm" onclick="InsertarOperador('+')"
                                    title="insertar operador suma: +">+</button>
                                <button class="btn btn-outline-dark btn-sm" onclick="InsertarOperador('-')"
                                    title="insertar operador resta: -">-</button>
                                <button class="btn btn-outline-dark btn-sm" onclick="InsertarOperador('*')"
                                    title="insertar operador multiplicación: *">*</button>
                                <button class="btn btn-outline-dark btn-sm" onclick="InsertarOperador('/')"
                                    title="insertar operador división: /">/</button>
                                <button class="btn btn-outline-dark btn-sm" onclick="InsertarOperador('^')"
                                    title="insertar operador potencia: ^">^</button>
                                <button class="btn btn-outline-dark btn-sm" onclick="InsertarParentesis()"
                                    title="insertar paréntesis: ()">( )</button>
                                <button class="btn btn-outline-dark btn-sm" onclick="InsertarAbs()"
                                    title="insertar valor absoluto: abs()">abs( )</button>
                            </div>
                        </div>
                        @else
                        <div class="col-12">
                            <h4>Formula:</h4>
                            <h5>{{ $formulaStored }}</h5>
                        </div>
                        @endif
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 text-center pb-1">
                            @can($permisoEditarModulo)
                                <button type="button" onclick="Validar()" class="btn btn-dark close-modal">
                                    <span class="ico ico-save"></span> Guardar
                                </button>
                            @endcan
                            <button type="button" wire:click.prevent="ResetUI()" data-bs-dismiss="modal"
                                aria-label="Cerrar" class="btn btn-outline-dark ">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
