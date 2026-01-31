@extends('layouts.' . \Auth::user()->role->nama . '.template')
@section('title', 'Admin | Input absensi')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Input Penugasan Pamong</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Penugasan
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
                {{-- <form action="javascript:void(0)" id="form_input_absensi" method="POST">
                    @csrf
                    <div class="modal fade" id="modal_add" role="dialog" aria-labelledby="modal_add">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Uploud Tugas </h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <span class="msg"></span>
                                    <div class="form-group">
                                        <input type="text" name="no" id="no" class="form-control" required
                                            autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="exampleFormControlFile1">File</label>
                                            <input type="file" class="form-control-file" name="tugas" id="tugas"
                                                required autocomplete="off">
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary"
                                        id="form-submit">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form> --}}

                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-primary" role="alert">
                            Input Penugasan dari Pamong {{ $pamong->nama }}
                        </div>
                        <div class="alert alert-success" role="alert">
                            Silahkan input penugasan yang diberikan oleh Pamong
                        </div>
                        <div class="card">

                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Nama Pamong</th>
                                            <td id="posko">{{ $pamong->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Penugasan</th>
                                            <td>{{ $penugasanPamong->penugasan }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Mulai</th>
                                            <td><span class="text-success">{{ $penugasanPamong->mulai }}</span></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Sampai</th>
                                            <td><span class="text-danger">{{ $penugasanPamong->selesai }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="{{ route('admin.penugasan.pamong.detail', ['idPamong' => $idPamong]) }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-circle-left mx-2"></i>Kembali</a>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" id="card_refresh"
                                        data-card-widget="card-refresh" data-source="{{ url()->current() }}"
                                        data-source-selector="#card-refresh-content" data-load-on-init="false">
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
                            <div class="card-body" id="card-refresh-content">
                                @foreach ($pamongPeserta as $item)
                                    @php
                                        $data = App\Models\PenugasanPamongDetail::where('pamong_peserta_id', $item->id)
                                            ->where('penugasan_pamong_id', $idPenugasanPamong)
                                            ->first();
                                        $link = App\Http\Services\GoogleDrive::link(@$data->path);
                                    @endphp
                                    @include('components.upload.file', [
                                        'dokumen' => $data == null ? false : true,
                                        'nama' => $item->nama_peserta,
                                        'ket' => $data ? $data->keterangan : null,
                                        'link' => $link,
                                        'id' => $item->id,
                                        'status' => 'Wajib',
                                        'fileDokumen' => 'file di database',
                                        'extension' => 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,avi,mkv,mp3,wav,ogg,aac',
                                    ])
                                @endforeach
                            </div>
                        </div>

                        <!-- /.card -->
                    </div>
                    @include('components.upload.modal')
                    <!-- /.col -->
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script>
        $('#form_update_dokumen').submit(function(e) {
            e.preventDefault();
            let fd = new FormData($("#form_update_dokumen")[0]);
            $.ajax({
                type: "POST",
                url: "{{ route('admin.penugasan.pamong.detail.input.simpan', ['idPamong' => $idPamong, 'idPenugasanPamong' => $idPenugasanPamong]) }}",
                data: fd,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('.overlay').remove();
                    var div =
                        '<div class="overlay" style="background-color: rgb(255, 255, 255, 0.7)">' +
                        '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                        '</div>';
                    $('.modal-content').append(div);
                    $('#form-submit').attr('disabled', true);
                },
                success: function(response) {
                    $('#form-submit').attr('disabled', false);
                    $('#dokumen-modal').modal('toggle');
                    swalToast(response.message, response.data);

                    // cardRefresh();
                    document.getElementById('card_refresh').click();
                },
                complete: function() {
                    $('.overlay').remove();
                    document.getElementById('card_refresh').click();
                    $('#pilih_file').html('Pilih File');
                },
            });
        });
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
                        "{{ route('admin.penugasan.pamong.detail.input.delete', ['idPamong' => $idPamong, 'idPenugasanPamong' => $idPenugasanPamong]) }}";
                    var fd = new FormData($(event.target)[0]);

                    $.ajax({
                        type: "post",
                        url: url,
                        data: fd,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            $(`.overlay card_file_${id}`).remove();
                            var div = `<div class="overlay card_file_${id}">` +
                                '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                                '</div>';
                            $('#card_file_' + id).append(div);
                        },
                        complete: function() {
                            $(`.overlay card_file_${id}`).remove();
                        },
                        success: function(response) {
                            swalToast(response.message, response.data);
                            cardRefresh();
                        }
                    });
                }
            })
        }

        $(document).on('click', '.btnDoc', function() {
            let ket = $(this).data('ket');
            let ext = $(this).data('ext');
            let no = $(this).data('id');
            $("#no").val(no);

            ext = ext.split(',');
            ext = ext.map(str => {
                return `.${str}`;
            }).toString();

            $('#keterangan-file').val(ket);
            $('#upload-file').attr('accept', ext);
            $('#ext').html(ext);

            $('#pilih_file').html('Pilih File');
            $('#upload-file').val('');

            $('.dokumen-modal').modal('show');
        });
    </script>
@endpush
