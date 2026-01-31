@extends('layouts.admin.template')
@section('title', 'Admin | Nilai')
@section('css')
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Nilai</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">/ Nilai
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @include('admin.nilai.filter')

                <div class="row">
                    <div class="col-12">
                        <div class="card" id="card">
                            <div class="card-header">

                                <!-- /.card-tools -->
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
                                    <table id="table1" class="table table-bordered table-striped table-hover w-100">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Jenis</th>
                                                <th>Tahun</th>
                                                <th>Program prodi</th>
                                                <th>NIM</th>
                                                <th>Nama</th>
                                                <th>Jenis Kelamin</th>
                                                <th>Nilai</th>
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
            var url = "{{ route('admin.nilai.data') }}"
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
                        d.tahun_id = $('#tahun_id').val();
                        d.prodi_id = $('#prodi_id').val();
                        d.jenis_kelamin = $('#jns').val();
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
                        data: 'peserta_jenis',
                        name: 'peserta_jenis',
                        className: "align-middle"
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
                        data: 'peserta_nim',
                        name: 'peserta_nim',
                        className: "align-middle",
                    },
                    {
                        data: 'peserta_nama',
                        name: 'peserta_nama',
                        className: "align-middle",
                    },
                    {
                        data: 'users_jenis_kelamin',
                        name: 'users_jenis_kelamin',
                        className: "align-middle",
                    },
                    {
                        data: 'nilai',
                        name: 'nilai',
                        className: "align-middle",
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: "align-middle",
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
