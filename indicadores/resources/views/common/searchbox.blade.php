<div class="no-print mr-05">
    <div class="input-group input-group-merge">
        <input type="text" wire:model.lazy="search" placeholder="buscar" class="form-control">
        @if($search != '')
        <span class="input-group-text cursor-pointer text-secondary btn-search" wire:click="$set('search', '')" title="Limpiar búsqueda">
            <i class="fa-solid fa-circle-xmark"></i>
        </span>
        @endif
        <button class="btn btn-relief-secondary d-flex flex-nowrap">
            <i class="fa-solid fa-search mr-05"></i>
            <span class="d-none d-sm-block">Buscar</span>
        </button>
    </div>
</div>