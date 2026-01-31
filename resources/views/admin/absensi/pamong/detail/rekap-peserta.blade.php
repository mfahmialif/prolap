{{-- TABEL DATA --}}
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary" id="card_peserta">
            <div class="card-header">
                <b>Rekapan Peserta PPL/PKL</b>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" id="card_refresh_peserta">
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
            <div class="card-body" id="card_body_peserta">
                <div class="table-responsive">
                    <table id="table_absensi_pamong_rekap" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                @foreach ($absensiStatus as $item)
                                    <th>{{ $item }}</th>
                                @endforeach
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
            $(document).ready(function() {
                // data table and card refresh
                var table1 = dataTablePeserta('#table_absensi_pamong_rekap');
                $('div.dataTables_filter input', table1.table().container()).focus();

                $('#card_refresh_peserta').click(function(e) {
                    table1.ajax.reload();
                });
            });
        });
    </script>
    <script>
        function dataTablePeserta(id) {
            var url = "{{ route('admin.absensi.pamong.detail.dataPeserta', ['idPamong' => $idPamong]) }}"
            var absensi = @json($absensiStatus);
            var columns = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false,
                    className: "align-middle"
                },
                {
                    data: 'peserta_nama',
                    name: 'peserta_nama',
                    className: 'align-middle'
                },
            ];
            absensi.forEach(status => {
                columns.push({
                    data: 'absensi_' + status,
                    name: 'absensi_' + status,
                    className: 'align-middle'
                });
            });

            
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
                        $('.overlay.peserta').remove();
                        var div = '<div class="overlay peserta">' +
                            '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                            '</div>';
                        $('#card_peserta').append(div);
                    },
                    complete: function() {
                        $('.overlay.peserta').remove();
                    }
                },
                deferRender: true,
                columns: columns,
            })

            datatable.buttons().container().appendTo(id + '_wrapper .col-md-6:eq(0)');
            return datatable;
        }

        function cardRefreshPeserta() {
            var cardRefreshPeserta = document.querySelector('#card_refresh_peserta');
            cardRefreshPeserta.click();
        }
    </script>
@endpush
