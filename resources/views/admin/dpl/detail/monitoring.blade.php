@extends('layouts.' . \Auth::user()->role->nama . '.template')
@section('title', 'Dosen | Monitoring')
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
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Monitoring</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dosen
                            </li>
                            <li class="breadcrumb-item active">Monitoring
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        @include('admin.dpl.detail.monitoring.absensi')
                    </div>
                    <div class="col-12">
                        @include('admin.dpl.detail.monitoring.penugasan')
                    </div>
                    <div class="col-12">
                        @include('admin.dpl.detail.monitoring.penilaian')
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->

    </div>

@endsection