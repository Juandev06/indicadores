@component('common.modal')
    @slot('modalTipo') {{ $selected_id > 0 ? 'edit' : 'store' }} @endslot
    @slot('modalTitulo') {{ $ComponentName }} @endslot

    @slot('contenido')
        <div class="row">
            <div class="col mb-1">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" wire:model.lazy="nombre" class="form-control" placeholder="Nombre categoria" required>
                    @error('nombre')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        @if ($selected_id > 0)
            <div class="row">
                <div class="col mb-1">
                    <div class="form-group">
                        <label>Estado *</label>
                        <select wire:model="estado" class="form-control" required>
                            <option value="A">Activo</option>
                            <option value="I">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
        @endif
        <hr>
    @endslot

@endcomponent
