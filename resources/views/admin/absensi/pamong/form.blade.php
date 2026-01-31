@extends('layouts.' . \Auth::user()->role->nama . '.template')
@section('title', 'Admin | Pamong')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Edit Absensi Pamong</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Absensi
                            </li>
                            <li class="breadcrumb-item">Pamong
                            </li>
                            <li class="breadcrumb-item active">Edit
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
                <div class="alert alert-primary" role="alert">
                    Edit Absensi oleh Pamong
                </div>
                <form action="#" method="post" id="form_edit">{{-- TABEL DATA --}}
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td scope="row">Nama</td>
                                                <td id="posko">{{ @$pamong->nama }}</td>
                                            </tr>
                                            <tr>
                                                <td scope="row">Pamong</td>
                                                <td>{{ @$pamong->pamong }}</td>
                                            </tr>
                                            <tr>
                                                <td>Pertemuan</td>
                                                <td><input type="text" name="pertemuan" class="form-control"
                                                        id="pertemuan" value="{{ $data->nama }}"
                                                        placeholder="Masukkan Keterangan Pertemuan"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                        <div class="col-12">
                            <div class="mb-1 mb-md-3">
                                <div class="row">
                                    @foreach (\Helper::getEnumValues('absensi_ps_pamong_detail', 'status') as $item)
                                        <div class="col-12 col-md">
                                            <button type="button"
                                                class="btn btn-{{ \Helper::getColorAbsensi($item) }} w-100 mb-2 mb-md-0"
                                                onclick="setAbsensi('{{ $item }}')">Set
                                                <b>{{ $item }}</b> Semua</button>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="form-text text-dark">*Klik tombol di atas untuk memudahkan absensi.</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card" id="card">
                                <div class="card-header">
                                    <a href="{{ route('admin.absensi.pamong.detail', ['idPamong' => $idPamong]) }}"
                                        class="btn btn-secondary">
                                        <i class="fa fa-arrow-circle-left mx-2"></i>Kembali</a>
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
                                        <table class="table table-striped" id="tablePesertaAbsensi">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5px">No</th>
                                                    <th style="width: 60%">Nama</th>
                                                    <th style="width: 35%">Absensi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.col -->
                                <button class="btn btn-primary btn_edit">Edit</button>
                            </div>
                        </div>

                </form>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            let max = 0;
            $(document).ready(function() {
                // data table and card refresh
                var table1 = dataTable('#tablePesertaAbsensi');
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
                $('.mintaku').each(function() {
                    $(this).select2();
                });
                table1.on('draw', function() {
                    $('.mintaku').select2({
                        theme: 'bootstrap4',
                    });
                });

                $(document).on('select2:open', () => {
                    document.querySelector('.select2-search__field').focus();
                });

            });

        });
    </script>
    <script>
        function dataTable(id) {
            var url =
                "{{ route('admin.absensi.pamong.detail.edit.dedit', ['idPamong' => $idPamong, 'idAbsensiPsPamong' => $idAbsensiPsPamong]) }}";
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
                searching: false,
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
                        data: 'nama_peserta',
                        name: 'nama_peserta',
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
            $('select:not(.normal)').each(function() {
                $(this).select2({
                    theme: 'bootstrap4',
                    dropdownParent: $(this).parent()
                });
            });
        }

        $("#form_edit").submit(function(e) {
            e.preventDefault();
            var alamat =
                "{{ route('admin.absensi.pamong.detail.edit.simpan', ['idPamong' => $idPamong, 'idAbsensiPsPamong' => $idAbsensiPsPamong]) }}";
            $.ajax({
                type: "POST",
                url: alamat,
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                    $('.btn_edit').attr('disabled', true);
                },
                success: function(response) {
                    $('.btn_edit').attr('disabled', false);
                    console.log(response);
                    swalToast(response.message, response.data);
                    if (response.message == 200) {
                        window.location.href =
                            "{{ route('admin.absensi.pamong.detail', ['idPamong' => $idPamong]) }}";
                    }
                    cardRefresh();

                }
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
        
        function setAbsensi(status) {
            $('.mintaku').val(status).trigger('change');
        }
    </script>
@endpush
