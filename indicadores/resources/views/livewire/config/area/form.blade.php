@component('common.modal')
    @slot('modalTipo') {{ $selected_id > 0 ? 'edit' : 'store' }} @endslot
    @slot('modalTitulo') {{ $ComponentName }} @endslot

    @slot('contenido')
        <div class="row gy-1 gx-2 5">
            <div class="col-12">
                <label>Nombre *</label>
                <input type="text" wire:model.lazy="name" class="form-control" placeholder="Nombre area" required>
                @error('name')
                    <span class="text-danger er">{{ $message }}</span>
                @enderror
            </div>
        </div>
        @if ($selected_id > 0)
            {{-- ESTADO --}}
            <div class="row gy-1 gx-2 ">
                <div class="col-12">
                    <label>Estado *</label>
                    <select wire:model="status" class="form-control  basic" required>
                        <option value="A">Activo</option>
                        <option value="I">Inactivo</option>
                    </select>
                    @error('status')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        @endif
        <hr>
    @endslot

@endcomponent
