@extends('layouts.' . \Auth::user()->role->nama . '.template')
@section('title', 'Nilai | Detail')
@section('css')
    <style>
        input[readonly],
        textarea[readonly] {
            background-color: #f8f9fa !important;
        }

        .select2-container--disabled .select2-selection__rendered {
            background-color: #f8f9fa !important;
        }
    </style>
@endsection
@section('content')
    </section>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Detail</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">/ Peserta
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @if (Session::has('tipe'))
                    <div class="alert alert-{{ Session::get('status') }} alert-dismissible fade show" role="alert">
                        {{ Session::get('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-3">
                        @include('admin.nilai.profil')
                    </div>
                    <!-- /.col -->
                    <div class="col-md-9" id="sideright">
                        <div class="card card-primary card-outline" id="card">
                            <div class="card-header p-2">
                                <div class="card-tools m-1">
                                    <button type="button" class="btn btn-tool" id="card_refresh"
                                        data-card-widget="card-refresh" data-source="{{ url()->current() }}"
                                        data-source-selector="#card-refresh-content" data-load-on-init="false">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                                <ul class="nav nav-pills" id="custom-tabs-one-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active " id="absensi-dpl-tab" data-toggle="pill"
                                            href="#absensi-dpl" role="tab" aria-controls="absensi-dpl"
                                            aria-selected="false">Absensi
                                            DPL</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="absensi-pengawas-tab" data-toggle="pill"
                                            href="#absensi-pengawas" role="tab" aria-controls="Absensi Pengawas"
                                            aria-selected="false">Absensi
                                            Pengawas</a>
                                    </li>
                                    {{-- <li class="nav-item">
                                        <a class="nav-link " id="custom-tabs-one-messages-tab" data-toggle="pill"
                                            href="#absensi-pamong" role="tab" aria-controls="Absensi Pamong"
                                            aria-selected="false">Absensi
                                            Pamong</a>
                                    </li> --}}
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-one-settings-tab" data-toggle="pill"
                                            href="#penugasan-dpl" role="tab" aria-controls="Penugasan DPL"
                                            aria-selected="true">Penugasan
                                            DPL</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link "id="custom-tabs-one-messages-tab" data-toggle="pill"
                                            href="#penugasan-pamong" role="tab" aria-controls="Penugasan Pamong"
                                            aria-selected="false">Penugasan
                                            Pamong</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body" id="card-refresh-content">
                                <div class="tab-content" id="custom-tabs-one-tabContent">
                                    <div class="tab-pane fade active show" id="absensi-dpl" role="tabpanel"
                                        aria-labelledby="absensi-dpl-tab">
                                        <!-- Konten untuk tab absensi dpl -->
                                        @php
                                            $n = 1;
                                        @endphp
                                        @foreach ($absensiDpl->data as $item)
                                            <h4><span class="badge badge-dark">{{ $item->posko->nama }}</span></h4>
                                            <hr>
                                            <h4>Daftar Pertemuan</h4>
                                            <ul class="list-group">
                                                @foreach ($item->absensi as $absensi)
                                                    <li class="list-group-item">
                                                        @if ($absensi['data'])
                                                            {{ $n++ }}. {{ $absensi['data']->absensi->nama }} -
                                                            {{ $absensi['data']->waktu_absen }}
                                                            <span
                                                                class="badge badge-{{ \Helper::getColorAbsensi($absensi['data']->status) }} float-right">
                                                                {{ $absensi['data']->status }}
                                                            </span>
                                                        @else
                                                            {{ $n++ }}. Belum Absensi -
                                                            {{ $absensi['absensi']->nama }}
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <h4 class="mt-4">Rekapan Kehadiran</h4>
                                            <ul class="list-group">
                                                @foreach (\Helper::getEnumValues('absensi_ps_dpl_detail', 'status') as $status)
                                                    <li class="list-group-item">Total {{ $status }}: <span
                                                            class="badge badge-info float-right">{{ $item->rekap[$status] }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endforeach
                                    </div>
                                    <div class="tab-pane fade" id="absensi-pengawas" role="tabpanel"
                                        aria-labelledby="absensi-pengawas-tab">
                                        <!-- Konten untuk tab absensi pengawas -->
                                        @php
                                            $n = 1;
                                        @endphp
                                        @foreach ($absensiPengawas->data as $item)
                                            <h4><span class="badge badge-dark">{{ $item->posko->nama }}</span></h4>
                                            <hr>
                                            <h4>Daftar Pertemuan</h4>
                                            <ul class="list-group">
                                                @foreach ($item->absensi as $absensi)
                                                    <li class="list-group-item">
                                                        @if ($absensi['data'])
                                                            {{ $n++ }}. {{ $absensi['data']->absensi->nama }} -
                                                            {{ $absensi['data']->waktu_absen }}
                                                            <span
                                                                class="badge badge-{{ \Helper::getColorAbsensi($absensi['data']->status) }} float-right">
                                                                {{ $absensi['data']->status }}
                                                            </span>
                                                        @else
                                                            {{ $n++ }}. Belum Absensi -
                                                            {{ $absensi['absensi']->nama }}
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <h4 class="mt-4">Rekapan Kehadiran</h4>
                                            <ul class="list-group">
                                                @foreach (\Helper::getEnumValues('absensi_ps_dpl_detail', 'status') as $status)
                                                    <li class="list-group-item">Total {{ $status }}: <span
                                                            class="badge badge-info float-right">{{ $item->rekap[$status] }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endforeach
                                    </div>

                                    {{-- <div class="tab-pane fade" id="absensi-pamong" role="tabpanel"
                                        aria-labelledby="absensi-pamong-tab">
                                        <!-- Konten untuk tab absensi pamong -->
                                        @php
                                            $n = 1;
                                        @endphp
                                        @foreach ($absensiPamong->data as $item)
                                            <h4><span class="badge badge-dark">{{ $item->pamong->nama }}</span></h4>
                                            <hr>
                                            <h4>Daftar Pertemuan</h4>
                                            <ul class="list-group">
                                                @foreach ($item->absensi as $absensi)
                                                    <li class="list-group-item">
                                                        @if ($absensi['data'])
                                                            {{ $n++ }}. {{ $absensi['data']->absensi->nama }} -
                                                            {{ $absensi['data']->waktu_absen }}
                                                            <span
                                                                class="badge badge-{{ \Helper::getColorAbsensi($absensi['data']->status) }} float-right">
                                                                {{ $absensi['data']->status }}
                                                            </span>
                                                        @else
                                                            {{ $n++ }}. Belum Absensi -
                                                            {{ $absensi['absensi']->nama }}
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <h4 class="mt-4">Rekapan Kehadiran</h4>
                                            <ul class="list-group">
                                                @foreach (\Helper::getEnumValues('absensi_ps_dpl_detail', 'status') as $status)
                                                    <li class="list-group-item">Total {{ $status }}: <span
                                                            class="badge badge-info float-right">{{ $item->rekap[$status] }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endforeach
                                    </div> --}}

                                    <div class="tab-pane fade" id="penugasan-dpl" role="tabpanel"
                                        aria-labelledby="penugasan-dpl-tab">
                                        <!-- Konten untuk penugasan dpl -->
                                        @php
                                            $n = 1;
                                        @endphp
                                        @foreach ($penugasanDpl->data as $item)
                                            <h4><span class="badge badge-dark">{{ $item->posko->nama }}</span></h4>
                                            <hr>
                                            <h4>Daftar Penugasan</h4>
                                            <ul class="list-group">
                                                @foreach ($item->penugasan as $penugasan)
                                                    <li class="list-group-item">
                                                        {{ $n++ }}.
                                                        {{ $penugasan['data']
                                                            ? $penugasan['data']->penugasanDpl->penugasan .
                                                                ' - ' .
                                                                \Helper::formatDateWithTime($penugasan['data']->waktu_pengumpulan)
                                                            : 'Belum Upload, Deadline: ' . \Helper::formatDateWithTime($penugasan['penugasan']->waktu_selesai) }}
                                                        @if ($penugasan['data'])
                                                            <a href="{{ \GoogleDrive::link($penugasan['data']->path) }}"
                                                                target="_blank"
                                                                class="float-right text-secondary"><u>Lihat
                                                                    Berkas <i class="fas fa-external-link-alt"></i></u></a>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <h4 class="mt-4">Rekapan Penugasan</h4>
                                            <ul class="list-group">
                                                <li class="list-group-item">Penugasan Selesai: <span
                                                        class="badge badge-info float-right">{{ $item->rekap['sudah'] }}</span>
                                                </li>
                                                <li class="list-group-item">Penugasan Belum: <span
                                                        class="badge badge-info float-right">{{ $item->rekap['belum'] }}</span>
                                                </li>
                                            </ul>
                                    </div>

                                    <div class="tab-pane fade" id="penugasan-pamong" role="tabpanel"
                                        aria-labelledby="penugsan-pamong-tab">
                                        <!-- Konten untuk penugasan pamong -->
                                        @php
                                            $n = 1;
                                        @endphp
                                        @foreach ($penugasanPamong->data as $item)
                                            <h4><span class="badge badge-dark">{{ $item->pamong->nama }}</span></h4>
                                            <hr>
                                            <h4>Daftar Penugasan</h4>
                                            <ul class="list-group">
                                                @foreach ($item->penugasan as $penugasan)
                                                    <li class="list-group-item">
                                                        {{ $n++ }}.
                                                        {{ $penugasan['data']
                                                            ? $penugasan['data']->penugasanPamong->penugasan .
                                                                ' - ' .
                                                                \Helper::formatDateWithTime($penugasan['data']->waktu_pengumpulan)
                                                            : 'Belum Upload, Deadline: ' . \Helper::formatDateWithTime($penugasan['penugasan']->waktu_selesai) }}
                                                        @if ($penugasan['data'])
                                                            <a href="{{ \GoogleDrive::link($penugasan['data']->path) }}"
                                                                target="_blank"
                                                                class="float-right text-secondary"><u>Lihat
                                                                    Berkas <i class="fas fa-external-link-alt"></i></u></a>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <h4 class="mt-4">Rekapan Penugasan</h4>
                                            <ul class="list-group">
                                                <li class="list-group-item">Penugasan Selesai: <span
                                                        class="badge badge-info float-right">{{ $item->rekap['sudah'] }}</span>
                                                </li>
                                                <li class="list-group-item">Penugasan Belum: <span
                                                        class="badge badge-info float-right">{{ $item->rekap['belum'] }}</span>
                                                </li>
                                            </ul>
                                        @endforeach
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div><!-- /.card-header -->
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
        </section>

    </div>
@endsection
