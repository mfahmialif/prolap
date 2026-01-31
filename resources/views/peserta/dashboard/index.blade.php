@extends('layouts.peserta.template')
@section('title', 'Dashboard')
@push('css')
    <style>
        .profile-picture-container {
            width: 150px;
            height: 150px;
            overflow: hidden;
        }

        .profile-user-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
@endpush
@section('content')
    <section class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        {{-- <li class="breadcrumb-item"><a href="#" class="active">dashboard</a></li> --}}
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    @include('peserta.dashboard.dashboard.profile')

                </div>

                <div class="col-md-9">

                    @include('peserta.dashboard.dashboard.informasi')
                    @include('peserta.dashboard.dashboard.biodata')
                    @include('peserta.dashboard.dashboard.dokumen')
                    @include('peserta.dashboard.dashboard.posko')
                    @include('peserta.dashboard.dashboard.kegiatan-mahasiswa')
                    @include('peserta.dashboard.dashboard.pamong')

                </div>

            </div>

        </div>
    </section>
    @include('peserta.dashboard.dashboard.setting')

@endsection

