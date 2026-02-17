@component('common.modal')
    @slot('modalTipo') {{ $idIndicador > 0 ? 'edit' : 'store' }} @endslot
    @slot('modalOpc') modal-lg @endslot
    @slot('modalTitulo') {{ $ComponentName }} @endslot

    @slot('contenido')
        <div class="row">
            {{-- NOMBRE --}}
            <div class="col-12">
                <label>Nombre *</label>
                <input type="text" wire:model.lazy="indicadorAct.nombre" class="form-control" placeholder="ej. Cantidad">
                @error('indicadorAct.nombre')
                    <span class="text-danger er">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mt-1">
            {{-- TIPO --}}
            <div class="col-sm-12 col-md-4 mb-1">
                <label>Tipo *</label>
                <select wire:model="indicadorAct.tipo" class="form-control basic" {!! $idIndicador > 0 ? 'onChange="alertaCambioPeriodo()"' : '' !!}>
                    <option value="N">Numérico</option>
                    <option value="P">Porcentual</option>
                </select>
                @error('indicadorAct.tipo')
                    <span class="text-danger er">{{ $message }}</span>
                @enderror
            </div>
            {{-- PERIODICIDAD --}}
            <div class="col-sm-12 col-md-4 mb-1">
                <label>Periodicidad *</label>
                <select wire:model="indicadorAct.id_periodo" class="form-control basic" {!! $idIndicador > 0 ? 'onChange="alertaCambioPeriodo()"' : '' !!}>
                    @foreach ($periodos as $periodo)
                        <option value="{{ $periodo->id }}"> {{ $periodo->nombre }} </option>
                    @endforeach
                </select>
                @error('indicadorAct.id_periodo')
                    <span class="text-danger er">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-12 col-md-4 mb-1">
                <div class="form-group">
                    <label>Calendario *</label>
                    <select wire:model="calendario" class="form-control basic">
                        <option value="1">Calendario Fiscal</option>
                        <option value="2">Calendario Tarifario</option>
                    </select>
                    @if (Session::get('calendario'))
                        <span class="text-danger er">{{ $message }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="row mt-1">
            {{-- TENDENCIA --}}
            <div class="col-sm-12 col-md-6 mb-1">
                <label>Tendencia *</label>
                <select name="tendencia" wire:model="indicadorAct.tendencia" class="form-control basic" {!! $idIndicador > 0 ? 'onChange="alertaCambioPeriodo()"' : '' !!}>
                    <option value="1">Creciente</option>
                    <option value="2">Decreciente</option>
                </select>
                @error('indicadorAct.tendencia')
                    <span class="text-danger er">{{ $message }}</span>
                @enderror
            </div>
            {{-- TOLERANCIA --}}
            <div class="col-sm-12 col-md-6 mb-1">
                <label>Tolerancia (%) *</label>
                <input type="text" wire:model="indicadorAct.tolerancia" class="form-control"
                    placeholder="Porcentaje de tolerancia para el indicador" {!! $idIndicador > 0 ? 'onClick="alertaCambioPeriodo()"' : '' !!}>
                @error('indicadorAct.tolerancia')
                    <span class="text-danger er">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mt-1">
            {{-- RESPONSABLE --}}
            <div class="col-sm-12 col-md-6 mb-1">
                <label>Responsable *</label>
                <select wire:model="indicadorAct.id_usuario" class="form-control basic">
                    <option value="0"> Seleccione </option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}"> {{ $usuario->name }} {{ $usuario->lastName }} </option>
                    @endforeach
                </select>
                @error('indicadorAct.id_usuario')
                    <span class="text-danger er">{{ $message }}</span>
                @enderror
            </div>
            @if ($idIndicador > 0)
                {{-- ESTADO --}}
                <div class="col-sm-12 col-md-6 mb-1">
                    <label>Estado *</label>
                    <select wire:model="indicadorAct.estado" class="form-control basic">
                        <option value="A">Activo</option>
                        <option value="I">Inactivo</option>
                    </select>
                    @error('indicadorAct.estado')
                        <span class="text-danger er">{{ $message }}</span>
                    @enderror
                </div>
            @endif
        </div>
        {{-- CATEGORIAS --}}
        <hr>
        <h4 class="mt-0">Categorías:</h4>
        <div class="row">
            @foreach ($categorias as $categoria)
                <div class="col-6 mt-1">
                    <label>{{ $categoria->nombre }} </label>
                    <select class="form-control basic" wire:model="categoriasSel.c{{ $categoria->id }}.id_subcategoria">
                        <option value="0"> Seleccione </option>
                        @foreach ($subcategorias->where('id_categoria', $categoria->id) as $subcategoria)
                            <option value="{{ $subcategoria->id }}"> {{ $subcategoria->nombre }} </option>
                        @endforeach
                    </select>
                </div>
            @endforeach
            @error('categoriasSelVal')
                <span class="text-danger er">{{ $message }}</span>
            @enderror
        </div>
        <hr>
    @endslot

@endcomponent
