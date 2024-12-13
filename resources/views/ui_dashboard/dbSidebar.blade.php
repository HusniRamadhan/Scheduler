<aside class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4">

    <a href="#" class="brand-link" style="width: 100%;">
        <img src="{{ asset('dist/img/UntanLogo.png') }}" alt="Untan Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">PENJADWALAN</span>
    </a>

    <div
        class="sidebar os-host os-theme-light os-host-overflow os-host-overflow-y os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-transition">
        <div class="os-resize-observer-host observed">
            <div class="os-resize-observer" style="left: 0px; right: auto;"></div>
        </div>
        <div class="os-size-auto-observer observed" style="height: calc(100% + 1px); float: left;">
            <div class="os-resize-observer"></div>
        </div>
        <div class="os-content-glue" style="margin: 0px -8px; width: 249px; height: 887px;"></div>
        <div class="os-padding">
            <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow-y: scroll;">
                <div class="os-content" style="padding: 0px 8px; height: 100%; width: 100%;">

                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="info" style="width: 100%; text-align: center;">
                            <a href="#" class="d-block">{{ Auth::user()->name }}</a>
                        </div>
                    </div>

                    @if (Auth::user()->role == 'admin')
                        <nav class="mt-2">
                            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                                data-accordion="false">
                                <li class="nav-item">
                                    <a href="/admin" {{-- class="nav-link {{ \Request::route()->getName() == 'admin' ? 'active text-light' : '' }}"> --}}
                                        class="nav-link  {{ request()->is('admin') ? 'active text-light' : '' }}">
                                        <i class="fas fa-home nav-icon"></i>
                                        <p>Dashboard</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/admin/setting"
                                        class="nav-link  {{ request()->is('admin/setting') ? 'active text-light' : '' }}">
                                        <i class="fas fa-id-card nav-icon"></i>
                                        <p>Pengaturan Akun</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/admin/pengguna"
                                        class="nav-link  {{ request()->is('admin/pengguna') ? 'active text-light' : '' }}">
                                        <i class="fas fa-user-plus nav-icon"></i>
                                        <p>Pengguna</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/admin/makul"
                                        class="nav-link  {{ request()->is('admin/makul') ? 'active text-light' : '' }}">
                                        <i class="fas fa-rectangle-list nav-icon"></i>
                                        <p>Mata Kuliah & Ruangan</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/admin/pengajar"
                                        class="nav-link  {{ request()->is('admin/pengajar') ? 'active text-light' : '' }}">
                                        <i class="fas fa-users-between-lines nav-icon"></i>
                                        <p>Pengajar</p>
                                    </a>
                                </li>
                                {{-- <li class="nav-item menu-is-opening menu-open"> --}}
                                <li
                                    class="nav-item {{ request()->is('admin/pralirs', 'admin/aktifmakul', 'admin/managekelas', 'admin/scheduling', 'admin/scheduling/input') ? 'menu-is-opening menu-open' : '' }}">
                                    <a href="#"
                                        class="nav-link {{ request()->is('admin/pralirs', 'admin/aktifmakul', 'admin/managekelas', 'admin/scheduling', 'admin/scheduling/input') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-edit"></i>
                                        <p>
                                            Penjadwalan
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview"
                                        style="display: {{ request()->is('admin/pralirs', 'admin/aktifmakul', 'admin/managekelas', 'admin/scheduling', 'admin/scheduling/input') ? 'block' : 'none' }};">
                                        <li class="nav-item">
                                            <a href="/admin/pralirs"
                                                class="nav-link {{ request()->is('admin/pralirs') ? 'active' : '' }}">
                                                <i class="nav-icon far fa-calendar-alt"></i>
                                                <p>Pra-LIRS</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/admin/aktifmakul"
                                                class="nav-link {{ request()->is('admin/aktifmakul') ? 'active' : '' }}">
                                                <i class="fas fa-book-open nav-icon"></i>
                                                <p>Aktivasi Mata Kuliah</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/admin/managekelas"
                                                class="nav-link {{ request()->is('admin/managekelas') ? 'active' : '' }}">
                                                <i class="fas fa-laptop-house nav-icon"></i>
                                                <p>Manajemen Kelas</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/admin/scheduling"
                                                class="nav-link {{ request()->is('admin/scheduling') ? 'active' : '' }}">
                                                <i class="fas fa-table-cells-column-lock nav-icon"></i>
                                                <p>Penjadwalan Mata Kuliah</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                        {{-- Member/User Sidebar --}}
                    @elseif (Auth::user()->role == 'member')
                        <nav class="mt-2">
                            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                                data-accordion="false">
                                <li class="nav-item">
                                    <a href="/user" {{-- class="nav-link {{ \Request::route()->getName() == 'user' ? 'active text-light' : '' }}"> --}}
                                        class="nav-link  {{ request()->is('user') ? 'active text-light' : '' }}">
                                        <i class="fas fa-home nav-icon"></i>
                                        <p>Dashboard</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/user/setting"
                                        class="nav-link  {{ request()->is('user/setting') ? 'active text-light' : '' }}">
                                        <i class="fas fa-id-card nav-icon"></i>
                                        <p>Pengaturan Akun</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/user/makul"
                                        class="nav-link  {{ request()->is('user/makul') ? 'active text-light' : '' }}">
                                        <i class="fas fa-rectangle-list nav-icon"></i>
                                        <p>Mata Kuliah</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/user/pralirs"
                                        class="nav-link  {{ request()->is('user/pralirs', 'user/pralirs/input') ? 'active text-light' : '' }}">
                                        <i class="nav-icon fas fa-edit"></i>
                                        <p>Pra-LIRS</p>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    @endif

                </div>
            </div>
        </div>
        <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
            <div class="os-scrollbar-track">
                <div class="os-scrollbar-handle" style="width: 100%; transform: translate(0px, 0px);"></div>
            </div>
        </div>
        <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-auto-hidden">
            <div class="os-scrollbar-track">
                <div class="os-scrollbar-handle" style="height: 56.381%; transform: translate(0px, 0px);"></div>
            </div>
        </div>
        <div class="os-scrollbar-corner"></div>
    </div>
    {{-- <div class="sidebar-custom">
        <a href="#" class="btn btn-link"><i class="fas fa-cogs"></i></a>
        <a href="#" class="btn btn-secondary hide-on-collapse pos-right">Help</a>
    </div> --}}
</aside>
