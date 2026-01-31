@extends('layouts.admin.template')
@section('title', 'Admin | Pamong')
@section('css')
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Pamong</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">/ Pamong
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                @include('admin.pamong.filter')

                <div class="row">
                    <div class="col-12">
                        <div class="card" id="card">
                            <div class="card-header">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_add">
                                    <i class="fas fa-plus-circle mx-2"></i>Tambah Data</button>
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
                                    <table id="table1" class="table table-bordered table-striped table-hover w-100">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Pamong</th>
                                                <th>Email</th>
                                                <th>HP</th>
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

        {{-- Modal Tambah --}}
        <form action="" id="form_add" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="modal fade" id="modal_add" tabindex="-1" role="dialog" aria-labelledby="modal_add">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Tambah Pamong </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input type="text" name="nama" id="nama_add" class="form-control"
                                    placeholder="Masukkan Nama Pamong" required>
                            </div>
                            <div class="form-group">
                                <label for="pamong">Pamong</label>
                                <input type="text" name="pamong" class="form-control"
                                    placeholder="Masukkan Pamong" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email <span class="text-warning">*Bisa
                                        dikosongi</span></label>
                                <input type="email" name="email" class="form-control"
                                    placeholder="Masukkan Email Pamong">
                            </div>
                            <div class="form-group">
                                <label for="hp">HP</label>
                                <input type="text" name="hp" class="form-control"
                                    placeholder="Masukkan Nomor HP Pamong">
                            </div>
                            <div class="form-group">
                                <label for="tahun_id">Tahun</label>
                                <select class="form-control select2bs4 w-100" name="tahun_id" required>
                                    <option value="">Pilih Tahun</option>
                                    @foreach ($tahun as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jenis_kelamin">Jenis Kelamin</label>
                                <select class="form-control select2bs4 w-100" name="jenis_kelamin" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    @foreach (\Helper::getEnumValues('users', 'jenis_kelamin') as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
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

        {{-- Modal Edit --}}
        <form action="#" method="POST" enctype="multipart/form-data" id="form_edit">
            @csrf
            <div class="modal fade" id="modal_edit" tabindex="-1" role="dialog" aria-labelledby="modal_edit"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="title_edit">Edit </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="id_edit">
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input type="text" name="nama" id="nama" class="form-control"
                                    placeholder="Masukkan Nama Pamong" required>
                            </div>
                            <div class="form-group">
                                <label for="pamong">Pamong</label>
                                <input type="text" id="pamong" name="pamong" class="form-control"
                                    placeholder="Masukkan Pamong" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email <span class="text-warning">*Bisa
                                        dikosongi</span></label>
                                <input type="email" name="email" id="email" class="form-control"
                                    placeholder="Masukkan Email Pamong">
                            </div>
                            <div class="form-group">
                                <label for="hp">HP</label>
                                <input type="text" name="hp" id="hp" class="form-control"
                                    placeholder="Masukkan Nomor HP Pamong">
                            </div>
                            <div class="form-group">
                                <label for="tahun_id">Tahun</label>
                                <select class="form-control select2bs4 w-100" id="tahun_id" name="tahun_id" required>
                                    <option value="">Pilih Tahun</option>
                                    @foreach ($tahun as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jenis_kelamin">Jenis Kelamin</label>
                                <select class="form-control select2bs4 w-100" id="jenis_kelamin" name="jenis_kelamin"
                                    required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    @foreach (\Helper::getEnumValues('users', 'jenis_kelamin') as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
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

    @include('admin.pamong.import')
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            //Initialize Select2 Elements
            $('select:not(.normal)').each(function() {
                $(this).select2({
                    theme: 'bootstrap4',
                    dropdownParent: $(this).parent()
                });
            });

            // data table and card refresh
            var table1 = dataTable('#table1');
            $('div.dataTables_filter input', table1.table().container()).focus();

            $('#card_refresh').click(function(e) {
                table1.ajax.reload();
            });

        });
    </script>

    <script>
        $('#modal_add').on('shown.bs.modal', function() {
            $('#nama_add').focus();
        })

        $('#form_add').submit(function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            var url = "{{ route('admin.pamong.add') }}";
            var fd = new FormData($(this)[0]);

            $.ajax({
                type: "post",
                url: url,
                data: fd,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#form_submit').attr('disabled', true);
                },
                success: function(response) {
                    $('#modal_add').modal('toggle');
                    $('#form_submit').attr('disabled', false);

                    swalToast(response.message, response.data);
                    cardRefresh();
                }
            });
        });

        $('#modal_edit').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nama = button.data('nama');
            var pamong = button.data('pamong');
            var hp = button.data('hp');
            var email = button.data('email');
            var jenis_kelamin = button.data('jenis_kelamin');
            var tahun_id = button.data('tahun_id');

            var modal = $(this);
            modal.find('#title_edit').text("Edit");
            modal.find('#id_edit').val(id);
            modal.find('#nama').val(nama);
            modal.find('#pamong').val(pamong);
            modal.find('#hp').val(hp);
            modal.find('#email').val(email);
            modal.find('#jenis_kelamin').val(jenis_kelamin).change();
            modal.find('#tahun_id').val(tahun_id).change();
        })

        $('#form_edit').submit(function(e) {
            e.preventDefault();

            var url = "{{ route('admin.pamong.edit') }}";
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

    <script>
        function deleteData(event) {
            event.preventDefault();
            var id = event.target.querySelector('input[name="id"]').value;
            Swal.fire({
                title: 'Yakin Ingin menghapus ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "{{ route('admin.pamong.delete') }}";
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
                            console.log(response);
                        }
                    });
                }
            })
        }

        function dataTable(id) {
            var url = "{{ route('admin.pamong.data') }}"
            var datatable = $(id).DataTable({
                // responsive: true,
                autoWidth: true,
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                search: {
                    return: true,
                },
                ajax: {
                    url: url,
                    data: function(d) {
                        d.jenis_kelamin = $('#f_jenis_kelamin').val()
                        d.tahun_id = $('#f_tahun_id').val()
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
                        data: 'nama',
                        name: 'nama',
                        className: "align-middle",
                    },
                    {
                        data: 'pamong',
                        name: 'pamong',
                        className: "align-middle",
                    },
                    {
                        data: 'users_email',
                        name: 'users_email',
                        className: "align-middle"
                    },
                    {
                        data: 'hp',
                        name: 'hp',
                        className: "align-middle"
                    },
                    {
                        data: 'users_jenis_kelamin',
                        name: 'users_jenis_kelamin',
                        className: "align-middle"
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: "align-middle",
                        'searchable': false,
                    },
                ]
            })
            datatable.buttons().container().appendTo(id + '_wrapper .col-md-6:eq(0)');
            return datatable;
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
