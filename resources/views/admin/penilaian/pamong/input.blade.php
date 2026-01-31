@extends('layouts.' . \Auth::user()->role->nama . '.template')
@section('title', 'Admin | Input Penilaian')
@push('css')
    <style>
        /* Chrome, Safari, Edge, Opera */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
@endpush
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Input Penilaian</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Penilaian
                            </li>
                            <li class="breadcrumb-item">Pamong
                            </li>
                            <li class="breadcrumb-item active">Input
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-success" role="alert">
                            Silahkan Input Penilaian
                        </div>
                        <div class="card card-outline card-success">
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Nama</th>
                                            <td id="posko">{{ @$pamong->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Pamong</th>
                                            <td>{{ @$pamong->pamong }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card" id="card">
                            <div class="card-header">
                                <a href="{{ route('admin.penilaian.pamong') }}" class="btn btn-secondary">
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
                                <form action="{{ route('admin.penilaian.pamong.input.store', ['pamong' => $pamong]) }}"
                                    id="form_input_penilaian" method="POST">
                                    @csrf
                                    <div class="table-responsive">
                                        <table id="table_penilaian_dpl_detail"
                                            class="table table-bordered table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIM</th>
                                                    <th>Nama Peserta</th>
                                                    @foreach ($komponenNilai as $item)
                                                        <th>{{ $item->nama }} ({{ $item->bobot }}%)</th>
                                                    @endforeach
                                                    <th>Nilai Akhir</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 mt-2">SIMPAN</button>
                                </form>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        var komponenNilai = @json($komponenNilai);

        $(document).ready(function() {
            // data table and card refresh
            var table1 = dataTable('#table_penilaian_dpl_detail');
            $('div.dataTables_filter input', table1.table().container()).focus();

            $('#card_refresh').click(function(e) {
                table1.ajax.reload();
            });
        });

        $('#form_input_penilaian').submit(function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            var url = "{{ route('admin.penilaian.pamong.input.store', ['pamong' => $pamong]) }}";
            var fd = new FormData($(this)[0]);

            $.ajax({
                type: "post",
                url: url,
                data: fd,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#form_input_penilaian button[type="submit"]').attr('disabled', true);
                },
                success: function(response) {
                    $('#form_input_penilaian button[type="submit"]').attr('disabled', false);
                    swalToast(response.message, response.data);
                    cardRefresh();
                }
            });
        });
    </script>
    <script>
        function dataTable(id) {
            var url = "{{ route('admin.penilaian.pamong.input.data', ['pamong' => $pamong]) }}"
            var columns = [];

            columns.push({
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                searchable: false,
                orderable: false,
                className: "align-middle"
            })
            columns.push({
                data: 'peserta_nim',
                name: 'peserta_nim',
                className: "align-middle"
            })
            columns.push({
                data: 'peserta_nama',
                name: 'peserta_nama',
                className: "align-middle"
            })
            komponenNilai.forEach(element => {
                columns.push({
                    data: 'nilai_' + element.nama,
                    name: 'nilai_' + element.nama,
                    className: "align-middle",
                    searchable: false,
                    orderable: false,
                })

            });
            columns.push({
                data: 'nilai_akhir',
                name: 'nilai_akhir',
                className: "align-middle",
                searchable: false,
                orderable: false,
            })


            var datatable = $(id).DataTable({
                // responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                "bPaginate": false,
                "order": [
                    [1, "asc"]
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
                columns: columns
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

        function setNilaiAkhir(id, event) {
            var nilaiElement = $(event.currentTarget).val();
            if (nilaiElement < 0) {
                $(event.currentTarget).val(0);
            }
            if (nilaiElement > 100) {
                $(event.currentTarget).val(100);
            }

            var nilaiAkhir = 0;
            komponenNilai.forEach(element => {
                var nilai = $('#nilai_' + element.nama + '_' + id).val();
                var bobot = element.bobot;
                nilaiAkhir += nilai * bobot / 100;
            });

            $('#nilai_akhir_' + id).val(nilaiAkhir);
        }
    </script>
@endpush
