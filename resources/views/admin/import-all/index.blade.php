@extends('layouts.admin.template')
@section('title', 'Admin')
@section('css')
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Import Data</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">/ Import Data
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('admin.import.store') }}" method="POST" id="form_import" enctype="multipart/form-data">
                            @csrf
                            <div class="card" id="card-import">
                                <div class="card-header">
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                            <i class="fas fa-expand"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <!-- /.card-tools -->
                                </div>
                                <div class="card-body">
                                    <div class="input-group">
                                      <div class="custom-file">
                                        <input type="file" class="custom-file-input" accept=".xlsx,.xls" id="file" name="file">
                                        <label class="custom-file-label" for="file">Choose file</label>
                                      </div>
                                    </div>
                                    {{--<div class="input-group mb-3">--}}
                                    {{--    <input type="file" class="form-control" id="file" name="file">--}}
                                    {{--    <label class="input-group-text" for="inputGroupFile02">File</label>--}}
                                    {{--</div>--}}
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary w-100" name="btn-upload" id="btn-upload">Import</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            $('#form_import').submit(function (e) {
                e.preventDefault();
                var lokasi =$('#form_import')[0];
                var formData = new FormData(lokasi);
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.import.store') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend:function(){
                        var div = `<div class="overlay">` +
                                '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                                '</div>';
                        $('#card-import').append(div);
                    },
                    complete:function(){
                        $(".overlay").remove();
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response != null) {
                            console.log(response);
                            message(response.title,response.message,response.type);
                        }
                    },
                    error: function (request, status, error) {
                        console.log(error, request);
                    }
                });
            });
        });

        function message(title,message,type) {
            Swal.fire({
                title: title,
                text: message,
                icon: type
            });
        }
    </script>
@endpush
