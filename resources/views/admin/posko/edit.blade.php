 {{-- Modal Edit --}}
 <form action="#" method="POST" enctype="multipart/form-data" id="form_edit">
     @csrf
     <div class="modal fade" id="modal_edit" role="dialog" aria-labelledby="modal_edit" aria-hidden="true">
         <div class="modal-dialog modal-lg" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h4 class="modal-title" id="title_edit">Edit </h4>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <input type="hidden" name="id" id="id_edit">
                     <div class="form-group">
                         <label for="tahun_id">Tahun</label>
                         <select class="form-control select2bs4 w-100" id="tahun_id" name="tahun_id" required>
                             <option value="">Pilih Tahun</option>
                             @foreach ($tahun as $item)
                                 <option value="{{ $item->id }}">{{ $item->kode }}</option>
                             @endforeach
                         </select>
                     </div>
                     <div class="form-group">
                         <label>Nama</label>
                         <input type="text" name="nama" id="nama" class="form-control"
                             placeholder="Masukkan Nama">
                     </div>
                     <div class="form-group">
                         <label>Lokasi</label>
                         <textarea name="lokasi" id="lokasi" class="form-control" placeholder="Masukkan Lokasi"></textarea>
                     </div>
                     <div class="form-group">
                         <label>Keterangan</label>
                         <textarea name="keterangan" id="keterangan" class="form-control" placeholder="Masukkan Keterangan"></textarea>
                     </div>
                 </div>
                 <div class="modal-footer justify-content-between">
                     <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                     <button type="submit" class="btn btn-primary" id="form_submit_edit">Simpan</button>
                 </div>
             </div>
         </div>
     </div>
 </form>

 @push('script')
     <script>
         $('#modal_edit').on('show.bs.modal', function(event) {
             var button = $(event.relatedTarget);
             var id = button.data('id');
             var nama = button.data('nama');
             var tahun_id = button.data('tahun_id');
             var lokasi = button.data('lokasi');
             var keterangan = button.data('keterangan');

             var modal = $(this);
             modal.find('#title_edit').text("Edit");
             modal.find('#id_edit').val(id);
             modal.find('#nama').val(nama);
             modal.find('#tahun_id').val(tahun_id).change();
             modal.find('#lokasi').val(lokasi);
             modal.find('#keterangan').val(keterangan);
         })

         $('#form_edit').submit(function(e) {
             e.preventDefault();

             var url = "{{ route('admin.posko.edit') }}";
             var fd = new FormData($('#form_edit')[0]);

             $.ajax({
                 type: "post",
                 url: url,
                 data: fd,
                 contentType: false,
                 processData: false,
                 beforeSend: function() {
                     $('#form_submit_edit').attr('disabled', true);
                 },
                 success: function(response) {
                     console.log(response);
                     $('#modal_edit').modal('toggle');
                     $('#form_submit_edit').prop("disabled", false);
                     swalToast(response.message, response.data);
                     cardRefresh();
                 }
             });
         });
     </script>
 @endpush
