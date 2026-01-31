@extends('layouts.' . \Auth::user()->role->nama . '.template')
@section('title', 'Admin | Input Dokumen Wajib')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Dokumen Wajib DPL</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Dokumen Wajib
                            </li>
                            <li class="breadcrumb-item">DPL
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

                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-primary" role="alert">
                            {{ $poskoDpl->nama_dpl }} | <b>{{ $poskoDpl->posko->nama }} ({{ $poskoDpl->posko->lokasi }})</b>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <b>Sebelum Mengisi Nilai</b>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" id="card_refresh_sebelum"
                                        data-card-widget="card-refresh" data-source="{{ url()->current() }}"
                                        data-source-selector="#card-refresh-content-sebelum" data-load-on-init="false">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div><!-- /.card-header -->
                            <div class="card-body" id="card-refresh-content-sebelum">
                                @include('components.upload.file', [
                                    'dokumen' => @$rubrikPenilaianDpl ? true : false,
                                    'nama' => 'Rubrik Penilaian',
                                    'ket' => @$rubrikPenilaianDpl->keterangan,
                                    'link' => \GoogleDrive::link(@$rubrikPenilaianDpl->path),
                                    'id' => 'rubrik_penilaian',
                                    'status' => 'Wajib',
                                    'fileDokumen' => 'file di database',
                                    'extension' =>
                                        'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,avi,mkv,mp3,wav,ogg,aac',
                                ])

                                @include('components.upload.file', [
                                    'dokumen' => @$beritaAcaraDpl ? true : false,
                                    'nama' => 'Berita Acara',
                                    'ket' => @$beritaAcaraDpl->keterangan,
                                    'link' => \GoogleDrive::link(@$beritaAcaraDpl->path),
                                    'id' => 'berita_acara',
                                    'status' => 'Wajib',
                                    'fileDokumen' => 'file di database',
                                    'extension' =>
                                        'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,avi,mkv,mp3,wav,ogg,aac',
                                ])

                                @include('components.upload.file', [
                                    'dokumen' => @$dokumentasiFotoDpl ? true : false,
                                    'nama' => 'Dokumentasi Foto',
                                    'ket' => @$dokumentasiFotoDpl->keterangan,
                                    'link' => \GoogleDrive::link(@$dokumentasiFotoDpl->path),
                                    'id' => 'dokumentasi_foto',
                                    'status' => 'Wajib',
                                    'fileDokumen' => 'file di database',
                                    'extension' =>
                                        'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,avi,mkv,mp3,wav,ogg,aac',
                                ])
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>

                <!-- /.row -->
                <div class="row">
                    <div class="col-12">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <b>Setelah Mengisi Nilai</b>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" id="card_refresh_setelah"
                                        data-card-widget="card-refresh" data-source="{{ url()->current() }}"
                                        data-source-selector="#card-refresh-content-setelah" data-load-on-init="false">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div><!-- /.card-header -->
                            <div class="card-body" id="card-refresh-content-setelah">
                                @include('components.upload.file', [
                                    'dokumen' => @$dokumentasiVideoDpl ? true : false,
                                    'nama' => 'Dokumentasi Video',
                                    'ket' => @$dokumentasiVideoDpl->keterangan,
                                    // 'link' => asset(@$dokumentasiVideoDpl->path),
                                    'link' => \GoogleDrive::link(@$dokumentasiVideoDpl->path),
                                    'id' => 'dokumentasi_video',
                                    'status' => 'Wajib',
                                    'fileDokumen' => 'file di database',
                                    'extension' =>
                                        'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,avi,mkv,mp3,wav,ogg,aac',
                                ])

                                <div class="callout callout-{{ $tugasAkhir ? 'success' : 'danger' }}">
                                    <h5>Upload Laporan Akhir Mahasiswa
                                        @if ($tugasAkhir)
                                            <span class="badge badge-success">Sudah</span>
                                        @else
                                            <span class="badge badge-danger">Belum</span>
                                        @endif
                                    </h5>
                                    <p><a href="{{ route('admin.penugasan.dpl.detail', ['idPoskoDpl' => $idPoskoDpl]) }}">Klik
                                            disini untuk upload</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    @include('components.upload.modal')
@endsection
@push('script')
    <script>
        $('#form_update_dokumen').submit(function(e) {
            e.preventDefault();
            let fd = new FormData($("#form_update_dokumen")[0]);
            $.ajax({
                type: "POST",
                url: "{{ route('admin.dokumen-wajib.dpl.input.simpan', ['idPoskoDpl' => $idPoskoDpl]) }}",
                data: fd,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('.overlay.update_dokumen').remove();
                    var div =
                        '<div class="overlay update_dokumen" style="background-color: rgb(255, 255, 255, 0.7)">' +
                        '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                        '</div>';
                    $('.modal-content').append(div);
                    $('#form-submit').attr('disabled', true);
                },
                success: function(response) {
                    $('#dokumen-modal').modal('toggle');
                    $('#form-submit').attr('disabled', false);
                    swalToast(response.message, response.data);
                    cardRefresh();
                },
                complete: function() {
                    $('.overlay.update_dokumen').remove();
                    document.getElementById('card_refresh_sebelum').click();
                    $('#pilih_file').html('Pilih File');
                },
            });
        });

        document.getElementById('upload-file').addEventListener('change', function(event) {
            const file = event.target.files[0]; // Get selected file
            const maxSize = {{ $maxSizeUpload }} * 1024;

            // if ($('#no').val() == "dokumentasi_video") {
            //     if (file.size > maxSize * 5) {
            //         Swal.fire({
            //             title: "Error",
            //             text: "File melebihi batas maksimum ukurannya: " + maxSize * 5 / 1024 / 1024 +
            //                 " MB",
            //             icon: "error"
            //         });
            //         event.target.value = ""; // Clear file input
            //         return;
            //     }
            // } else {
            //     if (file.size > maxSize) {
            //         Swal.fire({
            //             title: "Error",
            //             text: "File melebihi batas maksimum ukurannya: " + maxSize / 1024 / 1024 + " MB",
            //             icon: "error"
            //         });
            //         event.target.value = ""; // Clear file input
            //         return;
            //     }
            // }

            if (file.size > maxSize) {
                Swal.fire({
                    title: "Error",
                    text: "File melebihi batas maksimum ukurannya: " + maxSize / 1024 / 1024 + " MB",
                    icon: "error"
                });
                event.target.value = ""; // Clear file input
                return;
            }

        });
    </script>
    <script>
        function cardRefresh() {
            var cardRefreshSebelum = document.querySelector('#card_refresh_sebelum');
            cardRefreshSebelum.click();
            var cardRefreshSetelah = document.querySelector('#card_refresh_setelah');
            cardRefreshSetelah.click();
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
                        "{{ route('admin.dokumen-wajib.dpl.input.delete', ['idPoskoDpl' => $idPoskoDpl]) }}";
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
    </script>
    </script>
    <script>
        $(document).on('click', '.btnDoc', function() {
            let ket = $(this).data('ket');
            let ext = $(this).data('ext');
            let no = $(this).data('id');
            let nama = $(this).data('nama');

            $("#no").val(no);

            ext = ext.split(',');
            ext = ext.map(str => {
                return `.${str}`;
            }).toString();

            $('#nama-dokumen').html(nama);
            $('#keterangan-file').val(ket);
            $('#upload-file').attr('accept', ext);
            $('#ext').html(ext);

            $('#pilih_file').html('Pilih File');
            $('#upload-file').val('');

            $('.dokumen-modal').modal('show');
        });
    </script>
@endpush
