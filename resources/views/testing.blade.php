@extends('layouts.admin.template')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Testing</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">/ Testing
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @include('components.upload.file', [
                    'dokumen' => true,
                    'nama' => 'asd',
                    'link' => 'https://google.com',
                    'id' => 1,
                    'tipe' => 'Nama Mahasiswa',
                    'status' => 'Wajib',
                    'fileDokumen' => 'file di database',
                    'extension' => 'png,jpg,doc,docx',
                ])
                @include('components.upload.file', [
                    'dokumen' => false,
                    'nama' => 'Nama Mahasiswa',
                    'link' => 'https://google.com',
                    'id' => 1,
                    'tipe' => 'Nama Mahasiswa',
                    'status' => 'Wajib',
                    'fileDokumen' => 'file di database',
                    'extension' => 'png,jpg,doc,docx',
                ])
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

    @include('components.upload.modal')
@endsection
