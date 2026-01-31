 <div class="card" id="card{{ $id }}">
     <div class="card-header">
         <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_add{{ $id }}">
             <i class="fas fa-plus-circle mx-2"></i>Tambah Kegiatan</button>
         <div class="card-tools">
             <button type="button" class="btn btn-tool" id="card_refresh{{ $id }}"
                 onclick="cardRefresh{{ $id }}()">
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
     <div class="card-body" id="card_body_{{ $id }}">
         <div class="table-responsive">
             <table id="table_kegiatan_mahasiswa{{ $id }}"
                 class="table table-bordered table-striped table-hover w-100">
                 <thead>
                     <tr>
                         <th>No</th>
                         <th>Nama</th>
                         <th>Bukti</th>
                         <th>Tanggal</th>
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


 {{-- Modal Tambah --}}
 <form action="{{ route('peserta.dashboard.storeKegiatanMahasiswa') }}" id="form_add{{ $id }}"
     enctype="multipart/form-data" method="POST">
     @csrf
     <div class="modal fade" id="modal_add{{ $id }}" role="dialog"
         aria-labelledby="modal_add{{ $id }}" tabindex="-1">
         <div class="modal-dialog modal-lg">
             <div class="modal-content">
                 <div class="modal-header">
                     <h4 class="modal-title">Tambah Kegiatan Mahasiswa </h4>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <input type="hidden" name="posko_id" value="{{ $posko->id }}">
                     <div class="form-group">
                         <label>Nama Kegiatan</label>
                         <input type="text" name="nama_kegiatan" class="form-control"
                             placeholder="Masukkan Nama Kegiatan" required>
                     </div>
                     <div class="form-group">
                         <label>Tanggal</label>
                         <input type="datetime-local" name="tanggal" class="form-control" placeholder="Masukkan Tanggal"
                             required>
                     </div>
                     <div class="form-group">
                         <label>Keterangan</label>
                         <textarea name="keterangan" class="form-control" placeholder="Masukkan Keterangan"></textarea>
                     </div>
                     <div class="form-group">
                         <label for="image-dropzone{{ $id }}">Upload File <span
                                 class="text-secondary text-sm">*JPEG, PNG, JPG
                                 MAX 5MB | MAX 10 FILE</span></label>
                         <div id="container-dropzone">
                             <div class="needsclick dropzone" id="image-dropzone{{ $id }}">
                             </div>
                         </div>
                     </div>

                 </div>
                 <div class="modal-footer justify-content-between">
                     <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                     <button type="submit" id="form_submit{{ $id }}" class="btn btn-primary">Simpan</button>
                 </div>
             </div>
         </div>
     </div>
 </form>

 {{-- Modal Edit --}}
 <form action="#" method="POST" enctype="multipart/form-data" id="form_edit{{ $id }}">
     @csrf
     @method('PUT')
     <div class="modal fade" id="modal_edit{{ $id }}" role="dialog"
         aria-labelledby="modal_edit{{ $id }}" aria-hidden="true">
         <div class="modal-dialog modal-lg" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h4 class="modal-title" id="title_edit{{ $id }}">Edit </h4>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <input type="hidden" name="id">
                     <div class="form-group">
                         <label>Nama Kegiatan</label>
                         <input type="text" name="nama_kegiatan" class="form-control"
                             placeholder="Masukkan Nama Kegiatan" required>
                     </div>
                     <div class="form-group">
                         <label>Tanggal</label>
                         <input type="datetime-local" name="tanggal" class="form-control"
                             placeholder="Masukkan Tanggal" required>
                     </div>
                     <div class="form-group">
                         <label>Keterangan</label>
                         <textarea name="keterangan" class="form-control" placeholder="Masukkan Keterangan"></textarea>
                     </div>
                     <div class="form-group">
                         <label for="image-dropzone-edit{{ $id }}">Upload File <span
                                 class="text-secondary text-sm">*JPEG, PNG,
                                 JPG
                                 MAX 5MB | MAX 10 FILE</span></label>
                         <div id="container-dropzone">
                             <div class="needsclick dropzone" id="image-dropzone-edit{{ $id }}">
                             </div>
                         </div>
                     </div>

                     <label for="bukti">Bukti</label>
                     <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3" id="img-bukti{{ $id }}">

                     </div>
                     <div class="modal-footer justify-content-between">
                         <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                         <button type="submit" id="form_submit_edit{{ $id }}"
                             class="btn btn-primary">Simpan</button>
                     </div>
                 </div>
             </div>
         </div>
 </form>
 @include('peserta.dashboard.dashboard.kegiatan-mahasiswa.js-dropzone-add')
 @include('peserta.dashboard.dashboard.kegiatan-mahasiswa.js-dropzone-edit')
 @push('script')
     <script>
         var kegiatanMahasiswaId{{ $id }} = null;

         var tableKegiatanMahasiswa{{ $id }} = dataTable{{ $id }}(
             '#table_kegiatan_mahasiswa{{ $id }}');
         $('div.dataTables_filter input', tableKegiatanMahasiswa{{ $id }}.table().container()).focus();

         tableKegiatanMahasiswa{{ $id }}.on('draw.dt', function() {
             if (kegiatanMahasiswaId{{ $id }}) {
                 $('#action_kegiatan_mahasiswa' + kegiatanMahasiswaId{{ $id }}).click();
                 kegiatanMahasiswaId{{ $id }} = null;
             }
         });

         $('#form_add{{ $id }}').submit(function(e) {
             e.preventDefault();
             let fd = new FormData(this);
             let $submitBtn = $(this).find('[type=submit]'); // ambil tombol submit dalam form
             if (!$(this).find('[name="dokumen[]"]').length) {
                 swalToast(500, 'Dokumen harus diisi');
                 return;
             }
             $.ajax({
                 type: "POST",
                 url: "{{ route('peserta.dashboard.storeKegiatanMahasiswa') }}",
                 data: fd,
                 contentType: false,
                 processData: false,
                 beforeSend: function() {
                     $submitBtn.prop("disabled", true);
                 },
                 complete: function() {
                     $submitBtn.prop("disabled", false);
                 },
                 success: function(response) {
                     console.log(response);
                     $('#modal_add{{ $id }}').modal('hide');
                     swalToast(response.message, response.data);
                     if (response.message == 200) {
                         forgetDokumenSisa();
                     }
                     cardRefresh{{ $id }}();
                 }
             });
         });

         $('#modal_edit{{ $id }}').on('show.bs.modal', function(event) {
             var button = $(event.relatedTarget);
             var id = button.data('id');
             var nama_kegiatan = button.data('nama_kegiatan');
             var tanggal = button.data('tanggal');
             var keterangan = button.data('keterangan');
             var bukti = button.data('bukti');

             var modal = $(this);
             modal.find('#title_edit{{ $id }}').text("Edit - " + nama_kegiatan);
             modal.find('[name="id"]').val(id);
             modal.find('[name="nama_kegiatan"]').val(nama_kegiatan);
             modal.find('[name="tanggal"]').val(tanggal);
             modal.find('[name="keterangan"]').val(keterangan);

             $('#img-bukti{{ $id }}').empty();
             if (bukti.length < 1) {
                 $('#img-bukti{{ $id }}').append(`
                    <div class="col">
                        <div class="card shadow-sm w-100">
                            <div class="card-body">
                                <p class="card-text text-center">Tidak ada bukti.</p>
                            </div>
                        </div>
                    </div>
                `);
             }
             bukti.forEach(element => {
                 var imageExtension = ['jpg', 'jpeg', 'png', 'webp'];
                 $('#img-bukti{{ $id }}').append(`
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            ${!imageExtension.includes(element.file.split('.').pop().toLowerCase()) ?
                            `<button type="button" class="btn btn-success w-100" onclick="window.open('${element.url}')">Lihat File ${element.file}</button>
                             <button type="button" class="btn btn-danger w-100" onclick="deleteKegiatanMahasiswaBukti{{ $id }}(${element.id}, ${id})">Hapus</button>` :
                            `<a href="${element.url}" data-fancybox="gallery"
                                      data-caption="${element.file}">
                                      <img src="${element.url}" class="card-img-top"
                                          alt="Gambar 1">
                                  </a>
                                  <div class="card-footer p-0">
                                      <button type="button" class="btn btn-danger w-100" onclick="deleteKegiatanMahasiswaBukti{{ $id }}(${element.id}, ${id})">Hapus</button>
                                  </div>`
                            }
                        </div>
                    </div>
                `);
             });
             //  modal.find('[name="title_edit"]').text("Edit");
         })

         $('#form_edit{{ $id }}').submit(function(e) {
             e.preventDefault();

             var url = "{{ route('peserta.dashboard.updateKegiatanMahasiswa') }}";
             var fd = new FormData($('#form_edit{{ $id }}')[0]);

             $.ajax({
                 type: "post",
                 url: url,
                 data: fd,
                 contentType: false,
                 processData: false,
                 beforeSend: function() {
                     $('#form_submit_edit{{ $id }}').attr('disabled', true);
                 },
                 success: function(response) {
                     console.log(response);
                     if (response.message == 200) {
                         forgetDokumenSisa();
                     }
                     $('#modal_edit{{ $id }}').modal('toggle');
                     $('#form_submit_edit{{ $id }}').prop("disabled", false);
                     swalToast(response.message, response.data);
                     cardRefresh{{ $id }}();
                 },
                 error: function(xhr, status, error) {
                     console.log(xhr);
                     $('#form_submit_edit{{ $id }}').prop("disabled", false);
                     swalToast(xhr.responseJSON.message, xhr.responseJSON.data, 'error');
                 },
             });
         });

         function cardRefresh{{ $id }}() {
             stateDzDeleteLocal{{ $id }} = false;
             myDropzoneDokumen{{ $id }}.removeAllFiles(true);

             stateDzDeleteLocalEdit{{ $id }} = false;
             myDropzoneDokumenEdit{{ $id }}.removeAllFiles(true);

             document.getElementById('card_refresh{{ $id }}').click();
             tableKegiatanMahasiswa{{ $id }}.ajax.reload(null, false);
         }

         function dataTable{{ $id }}(id) {
             var url = "{{ route('peserta.dashboard.dataKegiatanMahasiswa', ['posko' => $posko->id]) }}"
             var datatable = $(id).DataTable({
                 // responsive: true,
                 autoWidth: true,
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
                         $('.overlay').remove();
                         var div = '<div class="overlay">' +
                             '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                             '</div>';
                         $('#card{{ $id }}').append(div);
                     },
                     complete: function() {
                         $('.overlay').remove();
                     }
                 },
                 deferRender: true,
                 columns: [{
                         data: 'id',
                         render: function(data, type, row, meta) {
                             return meta.row + meta.settings._iDisplayStart + 1;
                         },
                         className: "align-middle"
                     },
                     {
                         data: 'nama_kegiatan',
                         name: 'nama_kegiatan',
                         className: "align-middle"
                     },
                     {
                         data: 'bukti_kegiatan',
                         name: 'bukti_kegiatan',
                         className: "align-middle",
                     },
                     {
                         data: 'tanggal',
                         name: 'tanggal',
                         className: "align-middle",
                     },
                     {
                         data: 'action',
                         name: 'action',
                         className: "align-middle",
                         'searchable': false,
                     },
                 ]
             })
             datatable.buttons().container().appendTo(id + '_wrapper .col-md-6:eq(0)');
             return datatable;
         }

         function deleteKegiatanMahasiswaBukti{{ $id }}(id, kegiatanMahasiswaId) {
             Swal.fire({
                 title: 'Yakin Ingin menghapus ?',
                 icon: 'warning',
                 showCancelButton: true,
                 confirmButtonColor: '#3085d6',
                 cancelButtonColor: '#d33',
                 confirmButtonText: 'Iya',
                 cancelButtonText: 'Batal',
             }).then((result) => {
                 if (result.isConfirmed) {
                     var url = "{{ route('peserta.dashboard.deleteKegiatanMahasiswaBukti') }}";
                     $.ajax({
                         type: "post",
                         url: url,
                         data: {
                             id: parseInt(id),
                             _token: "{{ csrf_token() }}",
                             _method: 'DELETE'
                         },
                         success: function(response) {
                             $('#modal_edit{{ $id }}').modal('toggle');
                             kegiatanMahasiswaId{{ $id }} = kegiatanMahasiswaId;
                             swalToast(response.message, response.data);
                             cardRefresh{{ $id }}();
                             console.log(response);
                         }
                     });
                 }
             })
         }

         function deleteDataKegiatanMahasiswa{{ $id }}(event) {
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
                         "{{ route('peserta.dashboard.deleteKegiatanMahasiswa') }}";
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
                             $('#card{{ $id }}').append(div);
                         },
                         complete: function() {
                             $(`.overlay`).remove();
                         },
                         success: function(response) {
                             swalToast(response.message, response.data);
                             cardRefresh{{ $id }}();
                         }
                     });
                 }
             })
         }
     </script>
 @endpush
