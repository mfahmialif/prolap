<div class="card card-primary card-outline" id="card_posko">
    <div class="card-header">
        POSKO
        <div class="card-tools">
            <button type="button" class="btn btn-tool" id="card_refresh_posko" data-card-widget="card-refresh"
                data-source="{{ url()->current() }}" data-source-selector="#card_refesh_body_posko"
                data-load-on-init="false">
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
    <div class="card-body" id="card_refesh_body_posko">

        <div class="tab-content">
            <div class="post">
                <table class="w-100">
                    @foreach ($peserta->poskoPeserta as $poskoPeserta)
                        <tr>
                            <td width="200">Nama Posko</td>
                            <td>: {{ $poskoPeserta->posko->nama }}</td>
                        </tr>

                        <tr>
                            <td width="200">DPL</td>
                            <td>
                                @foreach ($poskoPeserta->posko->poskoDpl as $poskoDpl)
                                    <div>: {{ $poskoDpl->dpl->nama }}</div>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td width="200">Pengawas</td>
                            <td>
                                @foreach ($poskoPeserta->posko->poskoPengawas as $poskoPengawas)
                                    <div>: {{ $poskoPengawas->pengawas->nama }}</div>
                                @endforeach
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" class="w-100">
                                <div class="mt-3">
                                    @include('components.upload.file', [
                                        'dokumen' => @$penugasanDplTa[$poskoPeserta->posko->id]->link,
                                        'nama' => 'Tugas Akhir (Laporan) - ' . $poskoPeserta->posko->nama,
                                        'ket' => @$penugasanDplTa[$poskoPeserta->posko->id]->keterangan,
                                        'link' => @$penugasanDplTa[$poskoPeserta->posko->id]->link,
                                        'id' =>
                                            $poskoPeserta->posko->id . '|' . $poskoPeserta->id . '|Tugas Akhir',
                                        'status' => 'Wajib',
                                        'fileDokumen' => 'file di database',
                                        'extension' =>
                                            'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,avi,mkv,mp3,wav,ogg,aac',
                                    ])
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="w-100">
                                <div class="mt-3">
                                    @include('components.upload.file', [
                                        'dokumen' => @$penugasanDplVideo[$poskoPeserta->posko->id]->link,
                                        'nama' => 'VIDEO - ' . $poskoPeserta->posko->nama,
                                        'ket' => @$penugasanDplVideo[$poskoPeserta->posko->id]->keterangan,
                                        'link' => @$penugasanDplVideo[$poskoPeserta->posko->id]->link,
                                        'id' => $poskoPeserta->posko->id . '|' . $poskoPeserta->id . '|Video',
                                        'status' => 'Wajib',
                                        'fileDokumen' => 'file di database',
                                        'extension' => 'mp4,mov,avi,mkv',
                                    ])
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

    </div>
</div>

@include('components.upload.modal')

@push('script')
    <script>

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
        $('#form_update_dokumen').attr('action', "{{ route('peserta.dashboard.uploadPenugasanDpl') }}");
        $('#form_update_dokumen').submit(function(e) {
            e.preventDefault();
            let fd = new FormData($("#form_update_dokumen")[0]);
            $.ajax({
                type: "POST",
                url: $(this).attr('action'),
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
                    $('#dokumen-modal').modal('toggle');
                    $('#form-submit').attr('disabled', false);
                    swalToast(response.message, response.data);
                    document.getElementById('card_refresh_posko').click();
                },
                error: function(xhr, status, error) {
                    $('#dokumen-modal').modal('toggle');
                    $('#form-submit').attr('disabled', false);
                    swalToast(500, xhr.statusText + ' / File terlalu besar');
                },
                complete: function() {
                    $('.overlay').remove();
                    document.getElementById('card_refresh_posko').click();
                    $('#pilih_file').html('Pilih File');
                },
            });
        });

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
                        "{{ route('peserta.dashboard.deletePenugasanDpl') }}";
                    var fd = new FormData($(event.target)[0]);

                    $.ajax({
                        type: "post",
                        url: url,
                        data: fd,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            $(`.overlay`).remove();
                            var div = `<div class="overlay">` +
                                '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                                '</div>';
                            $('#card_posko').append(div);
                        },
                        complete: function() {
                            $(`.overlay`).remove();
                        },
                        success: function(response) {
                            swalToast(response.message, response.data);
                            document.getElementById('card_refresh_posko').click();
                        }
                    });
                }
            })
        }

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
