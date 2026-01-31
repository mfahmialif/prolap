<nav class="main-header navbar navbar-expand-md {{ $theme == 'light' ? 'navbar-dark navbar-custom' : 'navbar-dark' }}">
    <div class="container">
        <a href="{{ route('root') }}" class="navbar-brand">
            <img src="{{ asset('/homepage/images/logo-ponpes.png') }}" style="border-radius: 10px" alt="Logo prodi"
                class="brand-image">
            <span class="brand-text font-weight-light">{{ config('app.name') }} - Pengawas</span>
        </a>
        <button class="navbar-toggler order-3" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('pengawas.dashboard') }}"
                        class="nav-link {{ request()->routeIs('pengawas.dashboard') ? ' active-custom' : '' }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.pengawas.detailPosko', ['pengawas' => \Auth::user()->pengawas]) }}"
                        class="nav-link 
                        {{ request()->routeIs('admin.posko*') ? ' active-custom' : '' }}
                        {{ request()->routeIs('admin.pengawas.detailPosko') ? ' active-custom' : '' }}
                         ">Posko</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.pengawas.detailAbsensi', ['pengawas' => \Auth::user()->pengawas]) }}"
                        class="nav-link 
                        {{ request()->routeIs('admin.absensi*') ? ' active-custom' : '' }}
                        {{ request()->routeIs('admin.pengawas.detailAbsensi*') ? ' active-custom' : '' }} 
                        ">Absensi</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.pengawas.detailPenugasan', ['pengawas' => \Auth::user()->pengawas]) }}"
                        class="nav-link 
                        {{ request()->routeIs('admin.penugasan*') ? ' active-custom' : '' }}
                        {{ request()->routeIs('admin.pengawas.detailPenugasan*') ? ' active-custom' : '' }} 
                        ">Penugasan</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.pengawas.detailMonitoring', ['pengawas' => \Auth::user()->pengawas]) }}"
                        class="nav-link 
                        {{ request()->routeIs('admin.pengawas.detailMonitoring*') ? ' active-custom' : '' }} 
                        ">Monitoring</a>
                </li>
            </ul>
        </div>

        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
            <!-- Navbar date-->
            <li class="nav-item">
                <a class="nav-link  fw-bold d-none d-md-inline-block" href="#" role="button">
                    {{ date('d M Y') }}
                </a>
            </li>
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle profile-nav-custom" data-toggle="dropdown">
                    @if (\Auth::user()->foto != null)
                        <img src="{{ asset('foto/' . \Auth::user()->foto) }}" class="user-image img-circle elevation-1"
                            alt="User Image">
                    @else
                        <img src="{{ asset('/lte4/dist/img/user.png') }}" class="user-image img-circle elevation-1"
                            alt="User Image">
                    @endif
                    <span
                        class="d-none d-md-inline text-bold">{{ \Str::limit(strtoupper(\Auth::user()->nama), 15, '...') }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                    <li class="user-header text-white {{ $theme == 'light' ? 'bg-dalwa' : 'bg-dark' }}"
                        style="height: 150px;">
                        @if (\Auth::user()->foto != null)
                            <img src="{{ asset('foto/' . \Auth::user()->foto) }}" class="img-circle elevation-2"
                                alt="User Image">
                        @else
                            <img src="{{ asset('/lte4/dist/img/user.png') }}" class="img-circle elevation-2"
                                alt="User Image">
                        @endif
                        <p>
                            {{ @\Auth::user()->pengawas->nama }}
                        </p>
                    </li>

                    <li class="user-footer" style="margin-bottom:-20px">
                        <div class="menu-switcher {{ $theme }}-theme">
                            <div class="theme-switcher">
                                <input type="radio" id="light-theme" name="themes"
                                    {{ $theme == 'light' ? 'checked' : '' }} />
                                <label for="light-theme"
                                    onclick="location.href='{{ route('operasi.theme', ['theme' => 'light']) }}'">
                                    <span> <i data-feather="sun"></i>Light </span>
                                </label>
                                <input type="radio" id="dark-theme" name="themes"
                                    {{ $theme == 'dark' ? 'checked' : '' }} />
                                <label for="dark-theme"
                                    onclick="location.href='{{ route('operasi.theme', ['theme' => 'dark']) }}'">
                                    <span> <i data-feather="moon"></i>Dark </span>
                                </label>
                                <span class="slider"></span>
                            </div>
                        </div>
                    </li>

                    <li class="user-footer">
                        <a href="{{ route('admin.profil') }}" class="btn btn-default btn-flat">Profile</a>
                        <a href="{{ route('logout') }}" class="btn btn-default btn-flat float-right"
                            onclick="logout(event)">
                            Logout</p>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
