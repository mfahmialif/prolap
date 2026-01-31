@extends('layouts.admin.template')
@section('title', 'Pamong | Detail')
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
                        <h1>Pamong / Supervisor</h1>
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
                        @include('admin.pamong.detail.profil')
                    </div>
                    <!-- /.col -->
                    <div class="col-md-9" id="sideright">
                        @include('admin.pamong.detail.list')
                    </div>
                    <!-- /.col -->
                </div>
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->

    </div>

@endsection