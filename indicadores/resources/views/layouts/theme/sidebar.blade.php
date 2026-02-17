<div class="main-menu menu-fixed menu-light wrap-border menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto">
                <a class="navbar-brand" href="#">
                    <img src="{{ asset('img/logo.png') }}" alt="Indicadores - ID" width="160" height="40">
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse">
                    <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                    <i class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-primary" data-feather="disc"
                        data-ticon="disc"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="navigation-header">
                <span>{{ env('APP_TITLE') }}</span> <br>
                <span class="fs-10">v.{{ env('APP_VERSION') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-more-horizontal">
                    <circle cx="12" cy="12" r="1"></circle>
                    <circle cx="19" cy="12" r="1"></circle>
                    <circle cx="5" cy="12" r="1"></circle>
                </svg>
            </li>
            @foreach (Config::get('constantes.menu') as $menu)
                @if ($menu['tipo'] == 'menuitem')
                    @can($menu['permiso'])
                        <li class="nav-item {{ request()->path() == $menu['url'] ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ url($menu['url']) }}">
                                <span class="{{ $menu['ico'] }}" style="font-size:1.5rem"></span>
                                <span class="menu-title text-truncate" style="font-size:1.1rem">{{ $menu['nombre'] }}</span>
                            </a>
                        </li>
                    @endcan
                @else
                    @php
                        $comps = array_map(function ($n) {
                            return $n['permiso'];
                        }, $menu['items']);
                    @endphp
                    @can($menu['permiso'])
                    <li class="nav-item {{ in_array(request()->path(), $comps) ? 'has-sub sidebar-group-active open' : '' }}">
                        <a class="d-flex align-items-center" href="#">
                            <span class="{{ $menu['ico'] }}" style="font-size:1.5rem"></span>
                            <span class="menu-title text-truncate" style="font-size:1.1rem">{{ $menu['nombre'] }}</span>
                        </a>
                        <ul class="menu-content">
                            @foreach ($menu['items'] as $submenu)
                                @can($submenu['permiso'])
                                    <li class="nav-item {{ request()->path() == $submenu['url'] ? 'active' : '' }}">
                                        <a class="d-flex align-items-center" href="{{ url($submenu['url']) }}">
                                            @if($submenu['ico'] == '')
                                                <span class="fw-bold"> - &nbsp; </span>
                                            @else
                                                <span class="{{ $submenu['ico'] }}"></span>
                                            @endif
                                            <span class="menu-title text-truncate">{{ $submenu['nombre'] }}</span>
                                        </a>
                                    </li>
                                @endcan
                            @endforeach
                        </ul>
                    </li>
                    @endcan
                @endif
            @endforeach
        </ul>
    </div>
</div>
