@component('common.modal')
    @slot('modalTipo') {{ $selected_id > 0 ? 'edit' : 'store' }} @endslot
    @slot('modalOpc') modal-lg modal-dialog-scrollable @endslot
    @slot('modalTitulo') {{ $ComponentName }} @endslot

    @slot('contenido')
        <div class="row">
            <div class="col-sm-12 col-md-6 mb-1">
                <div class="form-group">
                    <label>Identificacion *</label>
                    <input type="text" wire:model.lazy="identification" class="form-control" placeholder="Número de documento">
                    @error('identification')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-1">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" wire:model.lazy="name" class="form-control" placeholder="Nombre">
                    @error('name')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-1">
                <div class="form-group">
                    <label>Apellidos *</label>
                    <input type="text" wire:model.lazy="lastName" class="form-control" placeholder="Apellidos ">
                    @error('lastName')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-1">
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" wire:model.lazy="phone" class="form-control" placeholder="Teléfono">
                    @error('phone')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-1">
                <div class="form-group">
                    <label>Email *</label>
                    <input type="text" wire:model.lazy="email" class="form-control" placeholder="Email">
                    @error('email')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-1">
                <div class="form-group">
                    <label>Dirección *</label>
                    <input type="text" wire:model.lazy="address" class="form-control" placeholder="Dirección">
                    @error('address')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-1">

                <div class="form-group">
                    <label>Contraseña *</label>
                    <input type="password" wire:model.lazy="password" class="form-control" placeholder="Contraseña">
                    @error('password')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                    @if (Session::get('failPass'))
                        <span class="text-danger er">{{ Session::get('failPass') }}</span>
                    @endif
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-1">
                <div class="form-group">
                    <label>Confirmar contraseña *</label>
                    <input type="password" wire:model.lazy="confirm_password" class="form-control" placeholder="Contraseña">
                    @error('password')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-1">
                <label>Area</label>
                <select wire:model="area_id" class="form-control  basic">
                    <option value="Elegir"> Seleccione </option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}"> {{ $area->name }} </option>
                    @endforeach
                </select>
                @error('area')
                    <span class="text-danger er">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-12 col-md-6 mb-1">
                <label>Perfil</label>
                <select wire:model="profile" class="form-control  basic">
                    <option value="Elegir"> Seleccione </option>
                    @foreach ($profiles as $profile)
                        <option value="{{ $profile->id }}"> {{ $profile->name }} </option>
                    @endforeach
                </select>
                @error('id_rol')
                    <span class="text-danger er">{{ $message }}</span>
                @enderror
            </div>
            @if ($selected_id > 0)
                <div class="col-sm-12 col-md-6 mb-1">
                    <label>Estado *</label>
                    <select wire:model="status" class="form-control  basic">
                        <option value="A">Activo</option>
                        <option value="I">Inactivo</option>
                    </select>
                </div>
            @endif
        </div>
        <hr>
    @endslot

@endcomponent
