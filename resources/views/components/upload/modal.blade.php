<form action="" method="post" id="form_update_dokumen" enctype="multipart/form-data">
    @csrf
    <div class="modal fade dokumen-modal" id="dokumen-modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span class="text-primary">UPLOAD FILE
                            :</span>
                        <span id="nama-dokumen"></span>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="upload-file">File</label>
                        <div class="input-group">
                            <input type="hidden" name="no" id="no">
                            <div class="custom-file">
                                <input type="file" name="file" class="custom-file-input" id="upload-file"
                                    {{-- accept=".png, .jpeg, .jpg, .pdf, .doc, .docx" required> --}} required>
                                <label class="custom-file-label" for="file" id="pilih_file">Pilih
                                    File</label>
                            </div>
                        </div>
                        <small><span class="text-danger">*</span>Ukuran file maksimal
                            {{ \BulkData::maxSizeUpload / 1024 }}MB.
                            Tipe file
                            :
                            <span id="ext"></span>
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="keterangan-file">Keterangan</label>
                        <textarea class="form-control" name="keterangan" id="keterangan-file" rows="3"s></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="form-submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- @push('script')
    <script>
        // $('#dokumen-modal').on('show.bs.modal', function(event) {
        //     let button = $(event.relatedTarget);
        //     let id = button.data('id');
        //     let tipe = button.data('tipe');
        //     let status = button.data('status');
        //     let dokumenPeserta = button.data('dokumen_peserta');
        //     let upload = button.data('upload');
        //     console.log(button.data());


        //     upload = upload.split(',');
        //     upload = upload.map(str => {
        //         return `.${str}`;
        //     }).toString();

        //     let modal = $(this);
        //     modal.find('#id_dokumen').val(id);
        //     modal.find('#no').val(id);
        //     modal.find('#status').html(status);
        //     modal.find('#tipe').val(tipe);
        //     modal.find('#upload').html(upload);
        //     modal.find('#file').attr('accept', upload);

        //     if (dokumenPeserta) {
        //         $('#pilih_file').html('Pilih File');
        //     }
        // })
    </script>
@endpush --}}
