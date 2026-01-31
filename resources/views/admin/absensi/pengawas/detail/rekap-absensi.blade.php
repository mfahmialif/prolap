{{-- TABEL DATA --}}
<div class="row">
    <div class="col-12">
        <div class="card" id="card">
            <div class="card-header">
                <a href="{{ route('admin.absensi.pws.input', ['idPoskoPengawas' => $idPoskoPengawas]) }}"
                    class="btn btn-primary">
                    <i class="fas fa-plus-circle mx-2"></i>Tambah Absensi</a>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">
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
                    <table id="table_absensi_pengawas"
                        class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                {{-- <th>No</th> --}}
                                <th>Nama</th>
                                <th>Pertemuan</th>
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
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
@push('script')
    <script>
        $(document).ready(function() {
            let max = 0;
            $(document).ready(function() {
                // data table and card refresh
                var table1 = dataTable('#table_absensi_pengawas');
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

            });

        });
    </script>
    <script>
        function dataTable(id) {
            var url = "{{ route('admin.absensi.pws.detail.data', ['idPoskoPengawas' => $idPoskoPengawas]) }}"
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
                        data: 'nama_posko',
                        name: 'nama_posko',
                        className: 'align-middle'
                    },
                    {
                        data: 'nama_pengawas',
                        name: 'nama_pengawas',
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
    <script>
        $(document).on('click', ".BtnRekap", function() {
            var id = $(this).data('id');
            var posko_pengawas_id = $(this).data('posko_pengawas_id');
            location.href = posko_pengawas_id + "/edit/" + id;
        });
        $(document).on('click', '.BtnDel', function() {
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            let posko_pengawas_id = $(this).data('posko_pengawas_id');
            var url =
                "{{ route('admin.absensi.pws.detail.hapus', ['idPoskoPengawas' => 'idPP', 'no' => 'idApp']) }}";
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
                    var alamat = url
                        .replace('idPP', posko_pengawas_id)
                        .replace('idApp', id);

                    $.ajax({
                        type: "Get",
                        url: alamat,
                        success: function(response) {
                            swalToast(response.message, response.data);
                            cardRefresh();
                            console.log(response);
                        }
                    });
                }
            })
        });
    </script>
@endpush