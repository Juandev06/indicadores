@component('common.modal')
    @slot('modalTipo') {{ $selected_id > 0 ? 'edit' : 'store' }} @endslot
    @slot('modalTitulo') {{ $ComponentName }} @endslot

    @slot('contenido')
        <div class="row">
            <div class="col-sm-12 col-md-12 mb-1">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" wire:model.lazy="nombre" class="form-control" placeholder="Nombre categoria" required>
                    @error('nombre')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 {{ $selected_id > 0 ? 'col-md-6' : 'col-md-12' }} mb-1">
                <div class="form-group">
                    <label>Categoria *</label>
                    <select wire:model="id_categoria" class="form-control  basic" required>
                        <option value="0"> Seleccione </option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}"> {{ $categoria->nombre }} </option>
                        @endforeach
                    </select>
                    @error('id_categoria')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            @if ($selected_id > 0)
                <div class="col-sm-12 col-md-6 mb-1">
                    <div class="form-group">
                        <label>Estado *</label>
                        <select wire:model="estado" class="form-control  basic" required>
                            <option value="A">Activo</option>
                            <option value="I">Inactivo</option>
                        </select>
                    </div>
                </div>
            @endif
        </div>
        <hr>
    @endslot

@endcomponent
