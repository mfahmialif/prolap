{{-- TABEL DATA --}}
<div class="row">

    <div class="col-12">
        <div class="card" id="card">
            <div class="card-header">
                <a href="{{ route('admin.absensi.pamong.input', ['idPamong' => $idPamong]) }}"
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
            <div class="card-body" id="card_body">
                <div class="table-responsive">
                    <table id="tableDetail" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pertemuan</th>
                                <th>Nama Pamong</th>
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
            // data table and card refresh
            var table1 = dataTable('#tableDetail');
            $('div.dataTables_filter input', table1.table().container()).focus();

            $('#card_refresh').click(function(e) {
                table1.ajax.reload();
            });

        });
    </script>
    <script>
        function dataTable(id) {
            var url = "{{ route('admin.absensi.pamong.detail.data', ['idPamong' => $idPamong]) }}"
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
                        // d.jenis_kelamin = $('#jns').val();
                        // d.prodi_id = $('#prodi_id').val();
                        d.posko_id = $('#posko_id').val();
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
                        data: 'nama',
                        name: 'nama',
                        className: "align-middle"
                    },
                    {
                        data: 'nama_pamong',
                        name: 'nama_pamong',
                        className: "align-middle"
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
        $(document).on('click', '.BtnEdit', function() {
            let id = $(this).data('id');
            let pamongId = $(this).data('pamong_id');
            location.href = pamongId + "/edit/" + id;
        });
        $(document).on('click', '.BtnDel', function() {
            var baseUrl =
                "{{ route('admin.absensi.pamong.detail.delete', ['idPamong' => 'PLACEHOLDER_ID', 'no' => 'PLACEHOLDER_POST_ID']) }}";
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            let pamongId = $(this).data('pamong_id');


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
                    var url = baseUrl
                        .replace('PLACEHOLDER_ID', pamongId)
                        .replace('PLACEHOLDER_POST_ID', id);

                    $.ajax({
                        type: "Get",
                        url: url,
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
