@extends('layouts.' . \Auth::user()->role->nama . '.template')
@section('title', 'Admin | Peserta')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Rekap Penugasan Pamong</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Penugasan
                            </li>
                            <li class="breadcrumb-item">Pamong
                            </li>
                            <li class="breadcrumb-item active">Rekap
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

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
                        <div class="alert alert-primary" role="alert">
                            Rekap Penugasan Pamong {{ $pamong->nama }}
                        </div>
                        <div class="card" id="card">
                            <div class="card-header">

                                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-circle-left mx-2"></i>Kembali</a>
                                <a class="btn btn-primary BtnTugas">
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Penugasan</a>
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
                                    <table id="table_absensi_dpl" class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Penugasan</th>
                                                <th>Mulai</th>
                                                <th>Selesai</th>
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
        <form action="#" id="form_add_ps_pamong" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="modal fade" id="modal_add_ps_pamong" role="dialog" aria-labelledby="modal_add_ps_pamong">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Tambah Penugasan</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <span class="msg"></span>
                            <div class="form-group">
                                <input type="hidden" name="no" id="no" value="{{ $idPamong }}"
                                    class="form-control" required autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label id="nama_add">Penugasan</label>
                                <input type="text" name="penugasan" id="penugasan" class="form-control" required
                                    autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label id="nama_add">Mulai</label>
                                <input type="datetime-local" name="mulai" id="mulai" class="form-control" required
                                    autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label id="nama_add">Selesai</label>
                                <input type="datetime-local" name="selesai" id="selesai" class="form-control" required
                                    autocomplete="off">
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" id="form_submit_ps_pamong">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <form action="#" method="POST" enctype="multipart/form-data" id="form_edit">
            @csrf
            <div class="modal fade" id="modal_edit" tabindex="-1" role="dialog" aria-labelledby="modal_edit"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="title_edit">Edit Penugasan DPL</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="id_edit">
                            <div class="form-group">
                                <label id="nama_add">Penugasan</label>
                                <input type="text" name="penugasan" id="penugasan_edit" class="form-control" required
                                    autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label id="nama_add">Mulai</label>
                                <input type="datetime-local" name="mulai" id="mulai_edit" class="form-control"
                                    required autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label id="nama_add">Selesai</label>
                                <input type="datetime-local" name="selesai" id="selesai_edit" class="form-control"
                                    required autocomplete="off">
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" id="form_submit_edit">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            let max = 0;
            $(document).ready(function() {
                // data table and card refresh
                var table1 = dataTable('#table_absensi_dpl');
                $('div.dataTables_filter input', table1.table().container()).focus();

                $('#card_refresh').click(function(e) {
                    table1.ajax.reload();
                });

                //Date range picker

                $('#posko_id').change(function(e) {
                    cardRefresh();
                });
                $('#prodi_id').change(function(e) {
                    cardRefresh();
                });
                $('#jns').change(function(e) {
                    cardRefresh();
                });

            });

        });
    </script>
    <script>
        function dataTable(id) {
            var url = "{{ route('admin.penugasan.pamong.detail.data', ['idPamong' => $idPamong]) }}";
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
                    // data: function(d) {
                    //     d.jenis_kelamin = $('#jns').val();
                    //     d.prodi_id = $('#prodi_id').val();
                    //     d.posko_id = $('#posko_id').val();
                    // },
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
                        data: 'penugasan',
                        name: 'penugasan',
                        className: 'align-middle'
                    },
                    {
                        data: 'mulai',
                        name: 'mulai',
                        className: 'align-middle'
                    },
                    {
                        data: 'selesai',
                        name: 'selesai',
                        className: 'align-middle'
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
    </script>
    <script>
        $(document).ready(function() {
            $(".BtnTugas").click(function(e) {
                e.preventDefault();
                $("#modal_add_ps_pamong").modal('show');
            });

            $('#form_add_ps_pamong').submit(function(e) {
                e.preventDefault();
                let a = "{{ route('admin.penugasan.pamong.detail.simpan', ['idPamong' => $idPamong]) }}"
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: a,
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function() {
                        $('#form_submit_ps_pamong').attr('disabled', true);
                    },
                    success: function(response) {
                        $('#form_submit_ps_pamong').attr('disabled', false);
                        swalToast(response.message, response.data);
                        cardRefresh();
                        $("#modal_add_ps_pamong").modal('hide');
                    }
                });

            });

        });

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

        function deleteData(event) {
            event.preventDefault();
            var id = event.target.querySelector('input[name="id"]').value;
            var nama = event.target.querySelector('input[name="nama"]').value;

            Swal.fire({
                title: `Yakin Ingin menghapus ${nama} ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "{{ route('admin.penugasan.pamong.detail.delete', ['idPamong' => $idPamong]) }}";
                    var fd = new FormData($(event.target)[0]);
                    $.ajax({
                        type: "post",
                        url: url,
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            swalToast(response.message, response.data);
                            cardRefresh();
                        }
                    });
                }
            })
        }
    </script>
    <script>
        $(document).on('click', '.BtnInputTugas', function() {
            let id = $(this).data('id');
            let pamong_id = $(this).data('pamong_id');
            location.href = pamong_id + '/input/' + id;
        });

        $('#modal_edit').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var penugasan = button.data('penugasan');
            var mulai = button.data('mulai');
            var selesai = button.data('selesai');

            var modal = $(this);
            modal.find('#title_edit').text("Edit " + penugasan);
            modal.find('#id_edit').val(id);
            modal.find('#penugasan_edit').val(penugasan);
            modal.find('#mulai_edit').val(mulai);
            modal.find('#selesai_edit').val(selesai);
        })

        $('#form_edit').submit(function(e) {
            e.preventDefault();

            var url = "{{ route('admin.penugasan.pamong.detail.edit', ['idPamong' => $idPamong]) }}";
            var fd = new FormData($('#form_edit')[0]);

            $.ajax({
                type: "post",
                url: url,
                data: fd,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#form_submit_edit').attr('disabled', true);
                },
                success: function(response) {
                    $('#modal_edit').modal('toggle');
                    $('#form_submit_edit').prop("disabled", false);

                    swalToast(response.message, response.data);
                    cardRefresh();
                }
            });
        });
    </script>
@endpush
