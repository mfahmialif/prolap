@extends('layouts.admin.template')
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
                        <h1>Peserta</h1>
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

                {{-- FILTER --}}
                @include('admin.peserta.filter')

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
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_add_peserta">
                                    <i class="fas fa-plus-circle mx-2"></i>Tambah Peserta
                                </button>
                                @include('admin.peserta.modal_add')
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
                                <!-- /.card-tools -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body" id="card_body">
                                <div class="table-responsive">
                                    <table id="table1" class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal Daftar</th>
                                                <th>Jenis</th>
                                                <th>Tahun</th>
                                                <th>Program prodi</th>
                                                <th>NIM</th>
                                                <th>Nama</th>
                                                <th>Jenis Kelamin</th>
                                                <th>Status</th>
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
    {{-- Modal Edit --}}
    <form action="#" method="POST" enctype="multipart/form-data" id="form_diterima">
        @csrf
        @method('PUT')
        <div class="modal fade" id="modal_diterima" tabindex="-1" role="dialog" aria-labelledby="modal_diterima"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="title_edit">Diterima? </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id_edit">
                        <div class="form-group row">
                            <label for="diterima" class="col-sm-3 col-form-label">Diterima</label>
                            <div class="col-sm-9">
                                <div class="form-control d-flex flex-wrap">
                                    <div class="custom-control custom-radio mr-3">
                                        <input class="custom-control-input" type="radio" id="diterima_iya" checked
                                            value="iya" name="diterima">
                                        <label for="diterima_iya" class="custom-control-label">Iya</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="diterima_tidak"
                                            value="tidak" name="diterima">
                                        <label for="diterima_tidak" class="custom-control-label">Tidak</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="prodi_diterima" class="col-sm-3 col-form-label">Prodi Pilihan Ke</label>
                            <div class="col-sm-9">
                                <div class="form-control d-flex flex-wrap">
                                    <div class="custom-control custom-radio mr-3">
                                        <input class="custom-control-input" type="radio" id="prodi_1" value="1"
                                            name="prodi_diterima">
                                        <label for="prodi_1" class="custom-control-label">Prodi Ke 1</label>
                                    </div>
                                    <div id="prodi_diterima_s1" class="d-flex flex-wrap">
                                        <div class="custom-control custom-radio mr-3">
                                            <input class="custom-control-input" type="radio" id="prodi_2"
                                                value="2" name="prodi_diterima">
                                            <label for="prodi_2" class="custom-control-label">Prodi Ke 2</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="prodi_3"
                                                value="3" name="prodi_diterima">
                                            <label for="prodi_3" class="custom-control-label">Prodi Ke 3</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                    <div class="card m-2">
                        <div class="card-body">
                            <div class="font-weight-bold text-md">Catatan : </div>
                            <div>Prodi 1 : <span id="prodi_1_catatan"></span></div>
                            <div id="prodi_s1_catatan">
                                <div>Prodi 2 : <span id="prodi_2_catatan"></span></div>
                                <div>Prodi 3 : <span id="prodi_3_catatan"></span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('script')
    <script>
        let max = 0;
        $(document).ready(function() {
            // data table and card refresh
            var table1 = dataTable('#table1');
            $('div.dataTables_filter input', table1.table().container()).focus();

            $('#card_refresh').click(function(e) {
                table1.ajax.reload(completeDataTable);
            });

            //Date range picker
            $('#range_tanggal').daterangepicker({
                locale: {
                    format: 'DD/MM/YYYY'
                }
            })

            $('#tanggal').change(function(e) {
                let tanggal = $(this).val();
                if (tanggal == "*") {
                    $("#form_group_range_tanggal").addClass('d-none');
                } else {
                    $("#form_group_range_tanggal").removeClass('d-none');
                }
            });

            $('#tahun_id').change(function(e) {
                cardRefresh();
            });
            $('#tanggal').change(function(e) {
                cardRefresh();
            });
            $('#range_tanggal').change(function(e) {
                cardRefresh();
            });
            $('#jenis_kelamin').change(function(e) {
                cardRefresh();
            });
            $('#status_id').change(function(e) {
                cardRefresh();
            });
            $('#prodi_id').change(function(e) {
                cardRefresh();
            });
            $('#jenis').change(function(e) {
                cardRefresh();
            });
        });
    </script>

    <script>
        $('#modal_diterima').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var prodi_diterima = button.data('prodi_diterima');
            var strata = button.data('strata');
            var prodi_1 = button.data('prodi_1');
            var prodi_2 = button.data('prodi_2');
            var prodi_3 = button.data('prodi_3');

            if (!prodi_diterima) {
                $('input[name="prodi_diterima"]').prop('checked', false);
            }

            if (strata == "S1") {
                $('#prodi_diterima_s1').removeClass('d-none');
                $('#prodi_diterima_s1').addClass('d-flex');
                $('#prodi_s1_catatan').removeClass('d-none');
                $('#prodi_2_catatan').html(prodi_2);
                $('#prodi_3_catatan').html(prodi_3);
            } else {
                $('#prodi_diterima_s1').removeClass('d-flex');
                $('#prodi_diterima_s1').addClass('d-none');
                $('#prodi_s1_catatan').addClass('d-none');
            }

            $('#prodi_1_catatan').html(prodi_1);

            var modal = $(this);
            modal.find('#title_edit').text("Update Status Aktif (DIterima)");
            modal.find('#id_edit').val(id);
            $("#prodi_" + prodi_diterima).prop("checked", true);
        })

        $('#form_diterima').submit(function(e) {
            e.preventDefault();

            let siswaId = $('#id_edit').val();
            var url = "{{ route('admin.peserta') }}/detail/" + siswaId + "/updateDiterima";
            var fd = new FormData($('#form_diterima')[0]);

            $.ajax({
                type: "post",
                url: url,
                data: fd,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    $('#modal_diterima').modal('toggle');
                    swalToast(response.message, response.data);
                    cardRefresh();
                }
            });
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
            var url = "{{ route('admin.peserta.data') }}"
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
                        d.tahun_id = $('#tahun_id').val();
                        d.tanggal = $('#tanggal').val();
                        d.range_tanggal = $('#range_tanggal').val();
                        d.status_id = $('#status_id').val();
                        d.jenis_kelamin = $('#jenis_kelamin').val();
                        d.prodi_id = $('#prodi_id').val();
                        d.jenis = $('#jenis').val();
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
                        data: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        className: "align-middle"
                    },
                    {
                        data: 'tanggal_daftar',
                        name: 'tanggal_daftar',
                        className: "align-middle"
                    },
                    {
                        data: 'jenis',
                        name: 'jenis',
                        className: "align-middle",
                    },
                    {
                        data: 'tahun_nama',
                        name: 'tahun_nama',
                        className: "align-middle",
                    },
                    {
                        data: 'prodi_nama',
                        name: 'prodi_nama',
                        className: "align-middle",
                    },
                    {
                        data: 'nim',
                        name: 'nim',
                        className: "align-middle",
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                        className: "align-middle",
                    },
                    {
                        data: 'jenis_kelamin',
                        name: 'jenis_kelamin',
                        className: "align-middle",
                    },
                    {
                        data: 'status_nama',
                        name: 'status_nama',
                        className: "align-middle",
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: "align-middle",
                        'searchable': false,
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
