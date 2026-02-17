<div>
    <nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item">
                        <a class="nav-link menu-toggle" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-menu ficon">
                                <line x1="3" y1="12" x2="21" y2="12"></line>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <line x1="3" y1="18" x2="21" y2="18"></line>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
            <ul class="nav navbar-nav align-items-center ms-auto">
                <li class="nav-item dropdown dropdown-user">
                    <a href="{{ url('logout') }}" class="nav-link dropdown-user-link">
                        <div class="user-nav d-sm-flex d-none">
                            <span class="user-name fw-bolder">{{ $usuarioAct->name }} {{ $usuarioAct->lastName }}</span>
                            <span class="user-status">{{ $areaUsuario }}</span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fa-solid fa-right-from-bracket fs-20"></span>
                            <span class="small">Salir</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>