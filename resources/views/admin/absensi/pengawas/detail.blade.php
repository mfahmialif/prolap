@extends('layouts.' . \Auth::user()->role->nama . '.template')
@section('title', 'Admin | Peserta')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Rekap Absensi Pengawas</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Absensi
                            </li>
                            <li class="breadcrumb-item">Pengawas
                            </li>
                            <li class="breadcrumb-item active">Rekap
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @if (\Auth::user()->role->nama == 'pengawas' || \Auth::user()->role->nama == 'admin')
                    <div class="alert alert-success alert-dismissible">
                        <h5><i class="icon fas fa-info"></i> Cetak Absensi Pengawas!</h5>
                        <a href="{{ route('admin.posko.cetakAbsensi', ['posko' => $data->posko, 'tipe' => 'pengawas']) }}"
                            target="_blank">Klik
                            disini untuk cetak absensi Pengawas <i class="fas fa-hand-pointer ml-1"></i></a>
                    </div>
                @endif

                @if (Session::has('type'))
                    @if (Session::get('type') == 'message')
                        <div class="alert alert-{{ Session::get('status') }} alert-dismissible fade show" role="alert">
                            {{ Session::get('message') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                @endif
                <div class="alert alert-primary" role="alert">
                    Berikut Informasi Rekapan Absensi dari Pengawas: <b> {{ $data->nama_pengawas }}</b>, untuk mengeditnya
                    bisa dengan
                    klik tombol KLIK, pilih Edit
                </div>
                <div class="alert alert-danger" role="alert">
                    Jika ingin menghapusnya, klik tombol KLIK, pilih Delete.
                </div>
                
                @include('admin.absensi.pengawas.detail.rekap-absensi')
                @include('admin.absensi.pengawas.detail.rekap-peserta')
            </div>
            <!-- /.container-fluid -->
        </section>
    </div>
@endsection

