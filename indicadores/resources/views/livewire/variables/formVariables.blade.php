@component('common.modal')
    @slot('modalTipo') {{ $idVariableAct > 0 ? 'edit' : 'store' }} @endslot
    @slot('modalOpc') modal-lg @endslot
    @slot('modalTitulo') {{ $pageTitle }} @endslot

    @slot('contenido')
        <div class="row">
            {{-- NOMBRE --}}
            <div class="col-sm-12 col-md-6 mb-1">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" wire:model.lazy="nombre" class="form-control" 
                        placeholder="Nombre descriptivo de la variable" maxlength="60">
                    @error('nombre')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            {{-- TIPO --}}
            <div class="col-sm-12 col-md-6 mb-1">
                <div class="form-group">
                    <label>Tipos *</label>
                    <select wire:model="tipo" class="form-control basic">
                        <option value="N">Numérico</option>
                        <option value="P">Porcentual</option>
                    </select>
                    @error('tipo')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            {{-- PERIODICIDAD --}}
            <div class="col-sm-12 col-md-6 mb-1">
                <div class="form-group">
                    <label>Periodicidad *</label>
                    <select wire:model="id_periodo" class="form-control basic" {{ $disableInput }}>
                        @foreach ($periodos as $periodo)
                            <option value="{{ $periodo->id }}">{{ $periodo->nombre }}</option>
                        @endforeach
                    </select>
                    @if (Session::get('variableFailTimes'))
                        <span class="text-danger er"> {{ Session::get('variableFailTimes') }}</span>
                    @endif
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-1">
                <div class="form-group">
                    <label>Calendario *</label>
                    <select wire:model="calendario" class="form-control basic" {{ $disableInput }}>
                        <option value="1">Calendario Fiscal</option>
                        <option value="2">Calendario Tarifario</option>
                    </select>
                    @if (Session::get('calendario'))
                        <span class="text-danger er"> {{ Session::get('calendario') }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            {{-- RESPONSABLE --}}
            <div class="col-sm-12 col-md-6 mb-1">
                <div class="form-group">
                    <label>Responsable *</label>
                    <select wire:model="id_usuario" class="form-control basic">
                        <option value="0">Seleccione el responsable</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"> {{ $user->name }} {{ $user->lastName }} </option>
                        @endforeach
                    </select>
                    @error('id_usuario')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            @if ($idVariableAct > 0)
                {{-- ESTADO --}}
                <div class="col-sm-12 col-md-6 mb-1">
                    <div class="form-group">
                        <label>Estado * </label>
                        <select wire:model="estado" class="form-control basic" {{ $disableInput }}>
                            <option value="A">Activo</option>
                            <option value="I">Inactivo</option>
                        </select>
                        @error('estado')
                            <span class="text-danger er">{{ $message }}</span>
                        @enderror
                        @if (Session::get('variableFail'))
                            <span class="text-danger er"> {{ Session::get('variableFail') }}</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        <hr>
    @endslot

@endcomponent
