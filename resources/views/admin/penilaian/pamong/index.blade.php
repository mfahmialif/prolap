@extends('layouts.' . \Auth::user()->role->nama . '.template')
@section('title', 'Admin | Penilaian Pamong')
@section('css')
    <style>
        .dataTables_paginate>span {
            background-color: #007bff;
            margin: 5px;
            padding: 7px;
            border-radius: 10px;
            cursor: pointer;
            color: white;
            border: none;
        }

        .dataTables_paginate>input {
            width: 50px;
            background-color: white;
            color: black;
            border: 1px solid #007bff;
            border-radius: 10px;
            margin: 5px;
            padding: 5px;
        }

        .dataTables_paginate>input:focus {
            border: none;
        }

        .dataTables_paginate>span:hover {
            background-color: rgb(183, 214, 255);
        }

        .dataTables_paginate>span:empty {
            display: none;
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
                        <h1>Penilaian <b>{{ @$pamong->nama }}</b></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Penilaian
                            </li>
                            <li class="breadcrumb-item active">Pamong
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="alert alert-primary" role="alert">
                    Untuk Input penilaian, bisa klik tombol KLIK, pilih Input Penilaian
                </div>

                {{-- FILTER --}}
                @include('admin.penilaian.pamong.filter')

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
                {{-- TABEL DATA --}}
                <div class="row">

                    <div class="col-12">
                        <div class="card" id="card">
                            <div class="card-header">
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" id="card_refresh">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                        <i class="fas fa-expand"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body" id="card_body">
                                <div class="table-responsive">
                                    <table id="tableAD" class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tahun</th>
                                                <th>Nama Pamong</th>
                                                <th>Pamong</th>
                                                <th>Jenis Kelamin</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->

    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // data table and card refresh
            var table1 = dataTable('#tableAD');
            $('div.dataTables_filter input', table1.table().container()).focus();

            $('#card_refresh').click(function(e) {
                table1.ajax.reload();
            });

            $('#tahun_id').change(function(e) {
                cardRefresh();
            });
            $('#jns').change(function(e) {
                cardRefresh();
            });

        });
    </script>
    <script>
        function dataTable(id) {
            var url = "{{ route('admin.penilaian.pamong.data') }}"
            var datatable = $(id).DataTable({
                // responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                search: {
                    return: true,
                },
                "pagingType": "input",
                ajax: {
                    url: url,
                    data: function(d) {
                        d.jenis_kelamin = $('#jns').val();
                        d.tahun_id = $('#tahun_id').val();
                        d.pamong_id = "{{ @$pamong->id }}";
                    },
                    beforeSend: function() {
                        $('.overlay').remove();
                        var div = '<div class="overlay">' +
                            '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                            '</div>';
                        $('#card').append(div);
                    },
                    complete: function() {
                        $('.overlay').remove();
                    }
                },
                deferRender: true,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false,
                        className: "align-middle"
                    },
                    {
                        data: 'tahun_kode',
                        name: 'tahun_kode',
                        className: "align-middle"
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                        className: "align-middle"
                    },
                    {
                        data: 'pamong',
                        name: 'pamong',
                        className: "align-middle"
                    },
                    {
                        data: 'users_jenis_kelamin',
                        name: 'users_jenis_kelamin',
                        className: "align-middle",
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: "align-middle",
                        searchable: false,
                        orderable: false,
                    },
                ],
            })
            datatable.buttons().container().appendTo(id + '_wrapper .col-md-6:eq(0)');
            return datatable;
        }


        function cardRefresh() {
            var cardRefresh = document.querySelector('#card_refresh');
            cardRefresh.click();
        }
    </script>
@endpush
