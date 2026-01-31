@php
    $role = Auth::user()->role->nama;
@endphp

<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-4 sidebar-{{ $theme }}-lime">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{ asset('/homepage/images/logo-ponpes.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="width: 33px;height:40px;object-fit:cover">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                @if (auth()->user()->foto != null)
                    <img src="{{ asset('/foto/' . auth()->user()->foto) }}" class="img-circle elevation-2"
                        style="width: 40px;height:40px;object-fit:cover" alt="User Image">
                @else
                    <img src="{{ asset('/img/logo uii dalwa.png') }}" class="img-circle elevation-2"
                        style="width: 40px;height:40px;object-fit:cover" alt="User Image">
                @endif
            </div>
            <div class="info">
                <a href="" class="d-block">{{ Str::limit(auth()->user()->nama, 11) }}</a>
            </div>
        </div>

        <div class="menu-switcher {{ $theme }}-theme">
            <div class="theme-switcher">
                <input type="radio" id="light-theme" name="themes" {{ $theme == 'light' ? 'checked' : '' }} />
                <label for="light-theme" onclick="location.href='{{ route('operasi.theme', ['theme' => 'light']) }}'">
                    <span> <i data-feather="sun"></i>Light </span>
                </label>
                <input type="radio" id="dark-theme" name="themes" {{ $theme == 'dark' ? 'checked' : '' }} />
                <label for="dark-theme" onclick="location.href='{{ route('operasi.theme', ['theme' => 'dark']) }}'">
                    <span> <i data-feather="moon"></i>Dark </span>
                </label>
                <span class="slider"></span>
            </div>

        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">

            <ul class="nav nav-pills nav-sidebar nav-collapse-hide-child nav-child-indent flex-column"
                data-widget="treeview" role="menu" data-accordion="false" id="list-sidebar">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"> <i
                            class="nav-icon fas fa-chart-line"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                @if ($role == 'admin')
                    <li class="nav-header">
                        Data
                    </li>
                    {{-- Pengguna --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.pengguna') }}"
                            class="nav-link {{ request()->routeIs('admin.pengguna') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-user"></i>
                            <p>
                                Pengguna
                            </p>
                        </a>
                    </li>
                @endif
                {{-- Prodi --}}
                @if ($role == 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.prodi') }}"
                            class="nav-link {{ request()->routeIs('admin.prodi') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-bookmark"></i>
                            <p>
                                Prodi
                            </p>
                        </a>
                    </li>
                @endif
                {{-- Pedoman --}}
                @if ($role == 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.pedoman') }}"
                            class="nav-link {{ request()->routeIs('admin.pedoman') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-file"></i>
                            <p>
                                Pedoman
                            </p>
                        </a>
                    </li>
                @endif

                {{-- Posko --}}
                @if ($role == 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.posko') }}"
                            class="nav-link {{ request()->routeIs('admin.posko*') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-home"></i>
                            <p>
                                Posko
                            </p>
                        </a>
                    </li>
                @endif

                @if ($role == 'admin')
                    {{-- DPL --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.dpl') }}"
                            class="nav-link {{ request()->routeIs('admin.dpl*') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-address-card"></i>
                            <p>
                                DPL
                            </p>
                        </a>
                    </li>
                @endif

                @if ($role == 'admin')
                    {{-- Pengawas --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.pengawas') }}"
                            class="nav-link {{ request()->routeIs('admin.pengawas*') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-user-secret"></i>
                            <p>
                                Pengawas
                            </p>
                        </a>
                    </li>
                @endif

                @if ($role == 'admin')
                    {{-- Pamong --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.pamong') }}"
                            class="nav-link {{ request()->routeIs('admin.pamong*') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-location-arrow"></i>
                            <p>
                                Pamong
                            </p>
                        </a>
                    </li>
                @endif
                @if ($role == 'admin')
                    {{-- Import Data --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.import') }}"
                            class="nav-link {{ request()->routeIs('admin.import*') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-file-import"></i>
                            <p>
                                Import Data
                            </p>
                        </a>
                    </li>
                @endif

                @if ($role == 'admin')
                    {{-- Peserta --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.peserta') }}"
                            class="nav-link {{ request()->routeIs('admin.peserta*') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-graduation-cap"></i>
                            <p>
                                Peserta
                            </p>
                        </a>
                    </li>
                @endif

                @if ($role = 'admin')
                    <li class="nav-header">
                        Dokumen Wajib
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.dokumen-wajib.dpl.index') }}"
                            class="nav-link
                            {{ request()->routeIs('admin.dokumen-wajib.dpl.index') ? 'active' : '' }}
                            {{ request()->routeIs('admin.dokumen-wajib.dpl.input*') ? 'active' : '' }}
                             ">
                            <i class="nav-icon fas fa-check-square"></i>
                            <p>
                                Dpl
                            </p>
                        </a>
                    </li>

                    <li class="nav-header">
                        Kegiatan Mahasiswa
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.kegiatan-mahasiswa') }}"
                            class="nav-link
                            {{ request()->routeIs('admin.kegiatan-mahasiswa*') ? 'active' : '' }}
                             ">
                            <i class="nav-icon fas fa-check-square"></i>
                            <p>
                                Posko
                            </p>
                        </a>
                    </li>

                    <li class="nav-header">
                        Jurnal Kegiatan
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.jurnal.pengawas.index') }}"
                            class="nav-link
                            {{ request()->routeIs('admin.jurnal.pengawas.index') ? 'active' : '' }}
                            {{ request()->routeIs('admin.jurnal.pengawas.input*') ? 'active' : '' }}
                             ">
                            <i class="nav-icon fas fa-check-square"></i>
                            <p>
                                Pengawas
                            </p>
                        </a>
                    </li>

                    <li class="nav-header">
                        Absensi
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.absensi.dpl') }}"
                            class="nav-link
                                {{ request()->routeIs('admin.absensi.dpl') ? 'active' : '' }}
                                {{ request()->routeIs('admin.absensi.detail*') ? 'active' : '' }}
                                 ">
                            <i class="nav-icon fas fa-check-square"></i>
                            <p>
                                Dpl
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.absensi.pws') }}"
                            class="nav-link {{ request()->routeIs('admin.absensi.pws*') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-check-square"></i>
                            <p>
                                Pengawas
                            </p>
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="{{ route('admin.absensi.pamong') }}"
                            class="nav-link {{ request()->routeIs('admin.absensi.pamong*') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-check-square"></i>
                            <p>
                                Pamong
                            </p>
                        </a>
                    </li> --}}
                    <li class="nav-header">
                        Penugasan
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.penugasan.dpl.index') }}"
                            class="nav-link {{ request()->routeIs('admin.penugasan.dpl*') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-tasks"></i>
                            <p>
                                Dpl
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.penugasan.pamong.index') }}"
                            class="nav-link {{ request()->routeIs('admin.penugasan.pamong*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>
                                Pamong
                            </p>
                        </a>
                    </li>
                @endif

                @if ($role = 'admin')
                    <li class="nav-header">
                        Penilaian
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.penilaian.dpl') }}"
                            class="nav-link {{ request()->routeIs('admin.penilaian.dpl*') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-gavel"></i>
                            <p>
                                Dpl
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.penilaian.pamong') }}"
                            class="nav-link {{ request()->routeIs('admin.penilaian.pamong*') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-gavel"></i>
                            <p>
                                Pamong
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.penilaian.peserta') }}"
                            class="nav-link {{ request()->routeIs('admin.penilaian.peserta*') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-gavel"></i>
                            <p>
                                Peserta
                            </p>
                        </a>
                    </li>
                @endif

                @if ($role == 'admin')
                    <li class="nav-header">
                        Nilai
                    </li>
                    {{-- Peserta --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.nilai') }}"
                            class="nav-link {{ request()->routeIs('admin.nilai') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-star"></i>
                            <p>
                                Peserta
                            </p>
                        </a>
                    </li>
                @endif
                <li class="nav-header">
                    Keuangan
                </li>

                @if ($role == 'admin' || $role == 'keuangan')
                    {{-- Pembayaran --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.pembayaran') }}"
                            class="nav-link {{ request()->routeIs('admin.pembayaran*') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-money-bill"></i>
                            <p>
                                Pembayaran
                            </p>
                        </a>
                    </li>
                @endif


                <li class="nav-header">
                    Settings
                </li>

                {{-- Komponen Nilai --}}
                @if ($role == 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.komponen-nilai') }}"
                            class="nav-link {{ request()->routeIs('admin.komponen-nilai') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-industry"></i>
                            <p>
                                Komponen Nilai
                            </p>
                        </a>
                    </li>
                @endif

                {{-- Biaya --}}
                @if ($role == 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.biaya') }}"
                            class="nav-link {{ request()->routeIs('admin.biaya') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-industry"></i>
                            <p>
                                Biaya
                            </p>
                        </a>
                    </li>
                @endif

                {{-- Tahun --}}
                @if ($role == 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.tahun') }}"
                            class="nav-link {{ request()->routeIs('admin.tahun') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-calendar"></i>
                            <p>
                                Tahun
                            </p>
                        </a>
                    </li>
                @endif

                {{-- Kuota --}}
                @if ($role == 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.kuota') }}"
                            class="nav-link {{ request()->routeIs('admin.kuota') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-calculator"></i>
                            <p>
                                Kuota
                            </p>
                        </a>
                    </li>
                @endif

                {{-- Jadwal --}}
                @if ($role == 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.jadwal') }}"
                            class="nav-link {{ request()->routeIs('admin.jadwal') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-clock"></i>
                            <p>
                                Jadwal
                            </p>
                        </a>
                    </li>
                @endif

                @if ($role == 'admin')
                    {{-- List Dokumen --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.list-dokumen') }}"
                            class="nav-link {{ request()->routeIs('admin.list-dokumen') ? 'active' : '' }}"> <i
                                class="nav-icon fas fa-file"></i>
                            <p>
                                List Dokumen
                            </p>
                        </a>
                    </li>
                @endif

                @if ($role == 'admin')
                    {{-- Setting --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.setting') }}"
                            class="nav-link {{ request()->routeIs('admin.setting*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                Website
                            </p>
                        </a>
                    </li>
                @endif
                {{-- Profil --}}
                <li class="nav-item">
                    <a href="{{ route('admin.profil') }}"
                        class="nav-link {{ request()->routeIs('admin.profil*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>
                            Profil
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link " onclick="logout(event)">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>
                            Logout
                        </p>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
    </div>
    <!-- /.sidebar -->
</aside>
