@extends('layouts.' . \Auth::user()->role->nama . '.template')
@section('title', 'Admin | Posko')
@section('css')
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Posko ({{ $posko->nama }})</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">/ Posko
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
                @include('admin.posko.detail.import-export')
                @if (\Auth::user()->role->nama == 'admin')
                    @include('admin.posko.detail.dpl')
                    @include('admin.posko.detail.pengawas')
                @endif
                @include('admin.posko.detail.peserta')
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->

    </div>
@endsection
