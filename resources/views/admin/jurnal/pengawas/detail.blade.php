@extends('layouts.' . \Auth::user()->role->nama . '.template')
@section('title', 'Admin | Rekap Jurnal Pengawas')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Rekap Jurnal Pengawas</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Jurnal
                            </li>
                            <li class="breadcrumb-item">Pengawas
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
                            Rekap Jurnal Pengawas <b>{{ $poskoPengawas->nama_pengawas }}</b> |
                            <b>{{ $poskoPengawas->posko->nama }}</b>
                        </div>
                        <div class="card" id="card">
                            <div class="card-header">
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-circle-left mx-2"></i>Kembali</a>
                                <a class="btn btn-primary BtnTugas">
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Jurnal</a>
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
                                                <th>Nama</th>
                                                <th>Tanggal</th>
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
        <form action="#" id="form_add" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="modal fade" id="modal_add" role="dialog" aria-labelledby="modal_add">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Tambah Jurnal</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <span class="msg"></span>
                            <div class="form-group">
                                <input type="hidden" name="no" id="no" value="{{ $idPoskoPengawas }}"
                                    class="form-control" required autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="nama_add">Nama Jurnal</label>
                                <input type="text" name="nama" id="nama" class="form-control" required
                                    autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <input type="datetime-local" name="tanggal" id="tanggal" class="form-control" required
                                    autocomplete="off">
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" id="form_submit">Simpan</button>
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
                            <h4 class="modal-title" id="title_edit">Edit Jurnal Pengawas</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="id_edit">
                            <div class="form-group">
                                <label for="nama_edit">Nama Jurnal</label>
                                <input type="text" name="nama" id="nama_edit" class="form-control" required
                                    autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="tanggal_edit">Tanggal</label>
                                <input type="datetime-local" name="tanggal" id="tanggal_edit" class="form-control"
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

                let now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); // Sesuaikan zona waktu lokal
                document.getElementById("tanggal").value = now.toISOString().slice(0, 16);
            });
        });
    </script>
    <script>
        function dataTable(id) {
            var url = "{{ route('admin.jurnal.pengawas.detail.data', ['idPoskoPengawas' => $idPoskoPengawas]) }}"
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
                        data: 'nama',
                        name: 'nama',
                        className: 'align-middle'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal',
                        className: 'align-middle'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: "align-middle",
                        'searchable': false,
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
                $("#modal_add").modal('show');
            });

            $('#form_add').submit(function(e) {
                e.preventDefault();
                let a =
                    "{{ route('admin.jurnal.pengawas.detail.simpan', ['idPoskoPengawas' => $idPoskoPengawas]) }}"
                $.ajax({
                    type: "POST",
                    url: a,
                    data: $("#form_add").serialize(),
                    dataType: "json",
                    beforeSend: function() {
                        $('#form_submit').attr('disabled', true);
                    },
                    complete: function() {
                        $('#form_submit').attr('disabled', false);
                    },
                    success: function(response) {
                        console.log(response);
                        swalToast(response.message, response.data);
                        cardRefresh();
                        $("#modal_add").modal('hide');
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
                    var url =
                        "{{ route('admin.jurnal.pengawas.detail.delete', ['idPoskoPengawas' => $idPoskoPengawas]) }}";
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
            let poskoPengawasId = $(this).data('posko_pengawas_id');
            location.href = poskoPengawasId + '/input/' + id;
        });

        $('#modal_edit').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nama = button.data('nama');
            var tanggal = button.data('tanggal');
            console.log(nama, tanggal);


            var modal = $(this);
            modal.find('#title_edit').text("Edit " + nama);
            modal.find('#id_edit').val(id);
            modal.find('#nama_edit').val(nama);
            modal.find('#tanggal_edit').val(tanggal);
        })

        $('#form_edit').submit(function(e) {
            e.preventDefault();

            var url = "{{ route('admin.jurnal.pengawas.detail.edit', ['idPoskoPengawas' => $idPoskoPengawas]) }}";
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
                    console.log(response);

                    swalToast(response.message, response.data);
                    cardRefresh();
                }
            });
        });
    </script>
@endpush
