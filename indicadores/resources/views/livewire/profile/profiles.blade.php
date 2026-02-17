<div>
    @include('layouts.theme.header')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            @component('common.contentHeaderRow')
                @slot('ComponentName')
                    {{ $ComponentName }}
                @endslot
                @slot('PageTitle')
                    {{ $PageTitle }}
                @endslot
            @endcomponent
            <div class="content-body card p-1">
                <div class="row mb-1">
                    <div class="col-6">
                        @include('common.searchbox')
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($profiles->count() <= 0)
                                <tr>
                                    <td colspan="7">
                                        <h6 class="text-center">No hay registros.</h6>
                                    </td>
                                </tr>
                            @endif
                            @foreach ($profiles as $profile)
                                <tr>
                                    <td>
                                        <h6>{{ $profile->name }}</h6>
                                    </td>
                                    <td class="text-center">
                                        <div class="td-content">
                                            <span
                                                class=" {{ $profile->status == 'ACTIVO' ? 'badge bg-success' : 'badge bg-danger' }}">
                                                {{ $profile->status }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $profiles->links() }}
                </div>
            </div>
        </div>
    </div>

</div>
