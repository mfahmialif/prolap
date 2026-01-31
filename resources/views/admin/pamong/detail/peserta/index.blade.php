@extends('layouts.' . \Auth::user()->role->nama . '.template')
@section('title', 'Pamong | Peserta')
@section('css')
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Pamong ({{ $pamong->nama }})</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">/ Pamong
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <button type="button" class="btn btn-secondary mb-3" onclick="location.href='{{ url()->previous() }}'">
                    <i class="fas fa-arrow-left mx-2"></i>Kembali</button>
                @include('admin.pamong.detail.peserta.import-export')
                @include('admin.pamong.detail.peserta.peserta')
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->

    </div>
@endsection
