@extends('layouts.' . \Auth::user()->role->nama . '.template')
@section('title', 'Admin | Kegiatan Mahasiswa')
@section('css')
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Kegiatan Mahasiswa
                            {{ isset($dpl) ? '(' . $dpl->nama . ')' : '' }}
                            {{ isset($pengawas) ? '(' . $pengawas->nama . ')' : '' }}
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">/ Kegiatan Mahasiswa
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                @include('admin.kegiatan-mahasiswa.filter')

                <div class="row">
                    <div class="col-12">
                        <div class="card" id="card">
                            <div class="card-header">
                                @if (isset($dpl))
                                    @if (\Auth::user()->role->nama == 'dpl')
                                        <button type="button" class="btn btn-secondary"
                                            onclick="location.href='{{ route('dpl.dashboard') }}'">
                                            <i class="fas fa-arrow-left mx-2"></i>Kembali</button>
                                    @else
                                        <button type="button" class="btn btn-secondary"
                                            onclick="location.href='{{ route('admin.dpl.detail', ['dpl' => $dpl]) }}'">
                                            <i class="fas fa-arrow-left mx-2"></i>Kembali</button>
                                    @endif
                                @elseif (isset($pengawas))
                                    @if (\Auth::user()->role->nama == 'pengawas')
                                        <button type="button" class="btn btn-secondary"
                                            onclick="location.href='{{ route('pengawas.dashboard') }}'">
                                            <i class="fas fa-arrow-left mx-2"></i>Kembali</button>
                                    @else
                                        <button type="button" class="btn btn-secondary"
                                            onclick="location.href='{{ route('admin.pengawas.detail', ['pengawas' => $pengawas]) }}'">
                                            <i class="fas fa-arrow-left mx-2"></i>Kembali</button>
                                    @endif
                                @else
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#modal_add">
                                        <i class="fas fa-plus-circle mx-2"></i>Tambah Data</button>
                                @endif
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
                                                <th>Tahun</th>
                                                <th>Kegiatan Mahasiswa</th>
                                                <th>Lokasi</th>
                                                <th>Keterangan</th>
                                                <th>Nama DPL</th>
                                                <th>Prodi DPL</th>
                                                <th>Pengawas</th>
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

        });
    </script>

    <script>

        function dataTable(id) {
            var url = "{{ route('admin.kegiatan-mahasiswa.data') }}"
            var datatable = $(id).DataTable({
                // responsive: true,
                autoWidth: true,
                processing: true,
                serverSide: true,
                stateSave: true,
                "order": [
                    [0, "desc"]
                ],
                search: {
                    return: true,
                },
                ajax: {
                    url: url,
                    data: function(d) {
                        d.dpl_id = "{{ @$dpl->id }}";
                        d.pengawas_id = "{{ @$pengawas->id }}";
                        d.tahun_id = $('#f_tahun_id').val();
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
                        data: 'lokasi',
                        name: 'lokasi',
                        className: "align-middle",
                    },
                    {
                        data: 'lokasi',
                        name: 'lokasi',
                        className: "align-middle",
                    },
                    {
                        data: 'dpl_nama',
                        name: 'dpl_nama',
                        className: "align-middle",
                    },
                    {
                        data: 'prodi_alias',
                        name: 'prodi_alias',
                        className: "align-middle",
                    },
                    {
                        data: 'pengawas_nama',
                        name: 'pengawas_nama',
                        className: "align-middle",
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
