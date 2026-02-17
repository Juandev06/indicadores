{{-- Slots:
    $modalOpc:
        Tamaños: extra small => modal-xs, small => modal-sm, medium => '', large => modal-lg, extra large => modal-xl
        Tamaños Fullscreen: 
            full screen => modal-fullscreen,
            pantalla menor a 576px => .modal-fullscreen-sm-down,
            pantalla menor a 768px => .modal-fullscreen-md-down,
            pantalla menor a 992px => .modal-fullscreen-lg-down,
            pantalla menor a 1200px => .modal-fullscreen-xl-down,
            pantalla menor a 1400px => .modal-fullscreen-xxl-down
        Modal con scroll: modal-dialog-scrollable
        Modal centrado verticalmente: modal-dialog-centered

    $modalNofade:
        no animar el modal

    $modalId:
        Id del modal (por defecto: modalForm)
    
    $modalTipo:
        Tipo de modal
            edit
            edit_funcion_personalizada()
            store
            store_funcion_personalizada()
            show
            funcion_personalizada()

    $modalCerrar:
        Accion para cerrar el modal (por defecto: ResetUI())

--}}
<div class="modal {{ isset($modalNofade) ? '' : 'fade' }}" tabindex="-1" id="{{ isset($modalId) ? $modalId : 'theModal' }}" aria-hidden="true" 
    wire:ignore.self wire:ignore-self data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog {{ isset($modalOpc) ? $modalOpc : '' }}">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" 
                    wire:click.prevent="{{ isset($modalCerrar) ? $modalCerrar : 'ResetUI()' }}"></button>
            </div>
            <div class="modal-body px-sm-1 mx-50 pb-2">
                @if(isset($modalTitulo))
                <h2 class="text-center mb-1" id="modalTitle">
                    {{-- Tipos de modal: edit, store, show (no muestra contenido) --}}
                    @if($modalTipo == 'edit')
                        <i class="fa-solid fa-pen-to-square"></i> Editar
                    @elseif($modalTipo == 'store')
                        <i class="fa-solid fa-square-plus"></i> Crear
                    @endif
                    {!! $modalTitulo !!}
                </h2>
                @endif
                @if(isset($modalSubtitulo))
                    <p class="text-center">{{ $modalSubtitulo }}</p>
                @endif
                @if(isset($modalTitulo))
                <hr>
                @endif
                <form id="modalForm" class="row gy-1 gx-2 mt-25" onsubmit="return false">
                    {!! $contenido !!}
                    <div class="row">
                        <div class="col-12 text-center pb-2">
                            @if($modalTipo == 'edit' || substr($modalTipo, 0, 5) == 'edit_')
                                <button type="button" wire:click.prevent="{{ $modalTipo == 'edit' ? 'Update()' : $modalTipo }}" 
                                    class="btn btn-dark close-modal">
                                    <i class="fa-solid fa-rotate"></i> Actualizar
                                </button>
                            @elseif($modalTipo == 'store'  || substr($modalTipo, 0, 6) == 'store_')
                                <button type="button" wire:click.prevent="{{ $modalTipo == 'store' ? 'Store()' : $modalTipo }}" class="btn btn-dark close-modal">
                                    <i class="fa-solid fa-floppy-disk"></i> Guardar
                                </button>
                            @elseif($modalTipo != 'show')
                                <button type="button" wire:click.prevent="{{ $modalTipo }}" class="btn btn-dark close-modal">
                                    <i class="fa-solid fa-rotate"></i> Guardar
                                </button>
                            @endif
                            <button type="button" wire:click.prevent="{{ isset($modalCerrar) ? $modalCerrar : 'ResetUI()' }}" 
                                data-bs-dismiss="modal" aria-label="Cerrar" class="btn btn-outline-dark ">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </form>
                @if(isset($contenidoAdicional))
                    {!! $contenidoAdicional !!}
                @endif
            </div>
        </div>
    </div>
</div>