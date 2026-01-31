 {{-- Modal Import --}}
 <form action="{{ route('admin.pamong.import') }}" method="POST" enctype="multipart/form-data" id="form_import">
     @csrf
     <div class="modal fade" id="modal_import" tabindex="-1" role="dialog" aria-labelledby="modal_import"
         aria-hidden="true">
         <div class="modal-dialog modal-lg" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h4 class="modal-title" id="title_edit">Import </h4>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <input type="hidden" name="id" id="id_edit">
                     <div class="form-group">
                         <label for="upload-file">File</label>
                         <div class="input-group">
                             <input type="hidden" name="no" id="no">
                             <div class="custom-file">
                                 <input type="file" name="file" class="custom-file-input" id="upload-file"
                                     required>
                                 <label class="custom-file-label" for="file" id="pilih_file">Pilih
                                     File</label>
                             </div>
                         </div>
                         <small><span class="text-danger">*</span>Ukuran file maksimal 20MB.
                             Tipe file
                             :
                             <span id="extension"></span>
                         </small>
                         <br>
                         <small><a href="{{ asset('template-excel/import.xlsx') }}" download><u>Klik disini untuk
                                     download template</u></a>
                         </small>
                     </div>
                 </div>
                 <div class="modal-footer justify-content-between">
                     <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                     <button type="submit" class="btn btn-primary" id="form_submit_import">Simpan</button>
                 </div>
             </div>
         </div>
     </div>
 </form>

 @push('script')
     <script>
         $('#modal_import').on('show.bs.modal', function(event) {
             var button = $(event.relatedTarget);
             var id = button.data('id');
             let extension = button.data('extension');

             extension = extension.split(',');
             extension = extension.map(str => {
                 return `.${str}`;
             }).toString();

             var modal = $(this);
             modal.find('#id_edit').val(id);
             $('#upload-file').attr('accept', extension);
             $('#extension').html(extension);
         })

         $('#form_import').submit(function(e) {
             e.preventDefault();

             var url = "{{ route('admin.pamong.import') }}";
             var fd = new FormData($('#form_import')[0]);

             $.ajax({
                 type: "post",
                 url: url,
                 data: fd,
                 contentType: false,
                 processData: false,
                 beforeSend: function() {
                     $('#form_submit_import').attr('disabled', true);
                 },
                 success: function(response) {
                     console.log(response);
                     $('#modal_import').modal('toggle');
                     $('#form_submit_import').prop("disabled", false);
                     swalToast(response.message, response.data);
                     if (typeof cardRefresh === 'function') {
                         cardRefresh();
                     }
                     if (document.getElementById('card_refresh_peserta')) {
                         document.getElementById('card_refresh_peserta').click();
                     }
                 }
             });
         });
     </script>
 @endpush
