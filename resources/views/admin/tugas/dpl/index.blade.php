@extends('layouts.' . \Auth::user()->role->nama . '.template')
@section('title', 'Admin | Penugasan Dpl')
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
                        <h1>Penugasan {{ @$pengawas ? 'Pengawas' : 'DPL' }} <b>{{ @$dpl->nama }}{{ @$pengawas->nama }}</b></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Penugasan
                            </li>
                            <li class="breadcrumb-item active">DPL
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                {{-- FILTER --}}
                @include('admin.tugas.dpl.filter')

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('admin.penugasan.dpl.createPenugasanVideo') }}" target="_blank" class="btn btn-danger mr-3"><i class="fas fa-plus"></i> Create Penugasan Video </a>
                    <a href="{{ route('admin.penugasan.dpl.createPenugasan') }}" target="_blank" class="btn btn-danger mr-3"><i class="fas fa-plus"></i> Create Penugasan TA</a>
                    <a href="{{ route('admin.penugasan.dpl.downloadExcel') }}" class="btn btn-success"><i class="fas fa-file-export"></i> Download Excel</a>
                </div>

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
                            <!-- /.card-header -->
                            <div class="card-body" id="card_body">
                                <div class="table-responsive">
                                    <table id="tablePD" class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tahun</th>
                                                <th>Nama Posko</th>
                                                <th>Nama Dpl</th>
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
        let max = 0;
        $(document).ready(function() {
            // data table and card refresh
            var table1 = dataTable('#tablePD');
            $('div.dataTables_filter input', table1.table().container()).focus();

            $('#card_refresh').click(function(e) {
                table1.ajax.reload(completeDataTable);
            });

            $('#prodi_id').change(function(e) {
                cardRefresh();
            });
            $('#jns').change(function(e) {
                cardRefresh();
            });
            $('#tahun_id').change(function(e) {
                cardRefresh();
            });

        });
    </script>
    <script>
        function dataTable(id) {
            var url = "{{ route('admin.penugasan.dpl.data') }}"
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
                        d.dpl_id = "{{ @$dpl->id }}";
                        d.pengawas_id = "{{ @$pengawas->id }}";
                        d.jenis_kelamin = $('#jns').val();
                        d.tahun_id = $('#tahun_id').val();
                        d.prodi_id = $('#prodi_id').val();
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
                        data: 'tahun_nama',
                        name: 'tahun_nama',
                        className: "align-middle"
                    },
                    {
                        data: 'nama_posko',
                        name: 'nama_posko',
                        className: "align-middle"
                    },
                    {
                        data: 'username',
                        name: 'username',
                        className: "align-middle",
                    },
                    {
                        data: 'jenis',
                        name: 'jenis',
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
                initComplete: function(setting, json) {
                    completeDataTable(json, setting);
                },
            })
            datatable.buttons().container().appendTo(id + '_wrapper .col-md-6:eq(0)');
            return datatable;
        }

        function completeDataTable(json, setting) {
            let data = json.data;
            if (data.length > 0) {
                max = json.recordsFiltered;
                $('#mulai').val(1);
                $('#sampai').val(max);
                $('#max').val(max);
            } else {
                $('#mulai').val(0);
                $('#sampai').val(0);
                $('#max').val(0);
            }
        }

        function cardRefresh() {
            var cardRefresh = document.querySelector('#card_refresh');
            cardRefresh.click();
        }

        function swalToast(message, data) {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            if (message == 200) {
                Toast.fire({
                    icon: 'success',
                    title: data
                });
            } else {
                Toast.fire({
                    icon: 'error',
                    title: data
                });
            }
        }
    </script>
@endpush
