@extends('layouts.' . \Auth::user()->role->nama . '.template')
@section('title', 'Admin | Peserta')
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
                        <h1>Absensi Pengawas <b>{{ @$pengawas->nama }}</b></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Absensi
                            </li>
                            <li class="breadcrumb-item active">Pengawas
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
                    Untuk melihat rekap absensi, bisa klik tombol KLIK, Pilih Rekap
                </div>
                <div class="alert alert-primary" role="alert">
                    Untuk Input absensi, bisa klik tombol KLIK, pilih Input Absensi
                </div>

                {{-- FILTER --}}
                @include('admin.absensi.pengawas.filter')

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('admin.absensi.pws.downloadExcel') }}" class="btn btn-success"><i class="fas fa-file-export"></i> Download Excel</a>
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
                                {{-- <a href="{{ route('register') }}" class="btn btn-primary">
                                        <i class="fas fa-plus-circle mx-2"></i>Tambah Peserta</a> --}}
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
                            <!-- /.card-tools -->
                            <div class="card-body" id="card_body">
                                <div class="table-responsive">
                                    <table id="tableAPWS" class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tahun</th>
                                                <th>Nama Posko</th>
                                                <th>Nama Pengawas</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
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
            var table1 = dataTable('#tableAPWS');
            $('div.dataTables_filter input', table1.table().container()).focus();

            $('#card_refresh').click(function(e) {
                table1.ajax.reload(completeDataTable);
            });

            //Date range picker

            $('#jenis_kelamin').change(function(e) {
                cardRefresh();
            });
            $('#tahun_id').change(function(e) {
                cardRefresh();
            });

        });
    </script>
    <script>
        $(document).on('click', '.BtnRekap', function() {
            let id = $(this).data('id');
            location.href = "{{ route('admin.absensi.pws') }}" + '/detail/' + id;

        });
        $(document).on('click', '.BtnInput', function() {
            let idPosko = $(this).data('posko_id');
            let idPoskoPengawas = $(this).data('id');
            let idPengawas = $(this).data('dpl_id');
            location.href = "{{ route('admin.absensi.pws') }}" + '/input/' + idPoskoPengawas;
        });
    </script>
    <script>
        function deleteData(event) {
            event.preventDefault();
            var id = event.target.querySelector('input[name="id"]').value;
            var nama = event.target.querySelector('input[name="nama"]').value;
            Swal.fire({
                title: `Yakin Ingin menghapus ${nama}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "{{ route('admin.peserta.delete') }}";
                    var fd = new FormData($(event.target)[0]);

                    $.ajax({
                        type: "post",
                        url: url,
                        data: fd,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            $('.overlay').remove();
                            var div = '<div class="overlay">' +
                                '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                                '</div>';
                            $('#card').append(div);
                        },
                        complete: function() {
                            $('.overlay').remove();
                        },
                        success: function(response) {
                            swalToast(response.message, response.data);
                            cardRefresh();
                            console.log(response);
                        }
                    });
                }
            })
        }

        function dataTable(id) {
            var url = "{{ route('admin.absensi.pws.data') }}"
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
                        d.jenis_kelamin = $('#jenis_kelamin').val();
                        d.tahun_id = $('#tahun_id').val();
                        d.pengawas_id = "{{ @$pengawas->id }}";
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
                        data: 'nama_pengawas',
                        name: 'nama_pengawas',
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
