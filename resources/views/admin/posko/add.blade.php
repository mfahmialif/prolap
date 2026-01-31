{{-- Modal Tambah --}}
<form action="" id="form_add" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="modal fade" id="modal_add" role="dialog" aria-labelledby="modal_add">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Posko </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tahun_id">Tahun</label>
                        <select class="form-control select2bs4 w-100" name="tahun_id" required>
                            <option value="">Pilih Tahun</option>
                            @foreach ($tahun as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label id="nama_add">Nama</label>
                        <input type="text" name="nama" id="nama_add" class="form-control"
                            placeholder="Masukkan Nama">
                    </div>
                    <div class="form-group">
                        <label>Lokasi</label>
                        <textarea name="lokasi" class="form-control" placeholder="Masukkan Lokasi"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" placeholder="Masukkan Keterangan"></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="form_submit">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('script')
    <script>
        $('#modal_add').on('shown.bs.modal', function() {

            $('#nama_add').focus();
        })

        $('#form_add').submit(function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            var url = "{{ route('admin.posko.add') }}";
            var fd = new FormData($(this)[0]);

            $.ajax({
                type: "post",
                url: url,
                data: fd,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#form_submit').attr('disabled', true);
                },
                success: function(response) {
                    console.log(response);
                    $('#modal_add').modal('toggle');
                    $('#form_submit').attr('disabled', false);

                    swalToast(response.message, response.data);
                    cardRefresh();
                }
            });
        });
    </script>
@endpush
