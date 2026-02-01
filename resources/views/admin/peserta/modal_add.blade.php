{{-- Modal Add Peserta --}}
<form action="" id="form_add_peserta" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="modal fade" id="modal_add_peserta" tabindex="-1" role="dialog" aria-labelledby="modal_add_peserta"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Peserta Baru</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- Search Section --}}
                    <div class="form-group">
                        <label for="search_add">Cari Mahasiswa (NIM / Nama)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search_add" placeholder="Masukkan NIM / Nama"
                                onfocus="this.select();">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" id="search_btn_add"
                                    onclick="getDataMahasiswa()">
                                    <i class="fa fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Cari datanya dulu, jika ada klik tombol cari maka formulir akan
                            terisi otomatis.</small>
                    </div>

                    <hr>

                    {{-- Form Fields --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nim_add">NIM <span class="text-danger">*</span></label>
                                <input type="text" name="nim" class="form-control" id="nim_add" required
                                    placeholder="Masukkan NIM">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_add">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" id="nama_add" required
                                    placeholder="Masukkan Nama Lengkap">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="prodi_id_add">Program Studi <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4" id="prodi_id_add" name="prodi_id" required>
                                    <option value="">Pilih Program Studi</option>
                                    @foreach ($prodi as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="jenis_kelamin_add">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4" id="jenis_kelamin_add" name="jenis_kelamin" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                     <div class="row">
                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="tahun_id_add">Tahun <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4" id="tahun_id_add" name="tahun_id" required>
                                    @foreach ($tahun as $t)
                                        <option value="{{ $t->id }}">{{ $t->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="status_id_add">Status <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4" id="status_id_add" name="status_id" required>
                                    <option value="">Pilih Status</option>
                                    @foreach ($status as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                     <div class="row">
                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="jenis_add">Jenis Kepesertaan <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4" id="jenis_add" name="jenis" required>
                                    <option value="Nasional">Nasional</option>
                                    <option value="Internasional">Internasional</option>
                                </select>
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_daftar_add">Tanggal Daftar <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_daftar" class="form-control" id="tanggal_daftar_add" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Optional Fields --}}
                     <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nik_add">NIK</label>
                                <input type="text" name="nik" class="form-control" id="nik_add"
                                    placeholder="Masukkan NIK">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomor_hp_add">Nomor HP</label>
                                <input type="text" name="nomor_hp" class="form-control" id="nomor_hp_add"
                                    placeholder="Masukkan Nomor HP">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="tempat_lahir_add">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" id="tempat_lahir_add"
                                    placeholder="Masukkan Tempat Lahir">
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_lahir_add">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" id="tanggal_lahir_add">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="alamat_add">Alamat</label>
                        <textarea name="alamat" class="form-control" id="alamat_add" rows="2" placeholder="Masukkan Alamat"></textarea>
                    </div>

                     <div class="row">
                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="nama_pondok_add">Nama Pondok</label>
                                <input type="text" name="nama_pondok" class="form-control" id="nama_pondok_add"
                                    placeholder="Masukkan Nama Pondok">
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label for="keterangan_add">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control" id="keterangan_add"
                                    placeholder="Keterangan tambahan">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn_simpan_peserta">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('script')
<script>
    $(document).ready(function() {
        // Set default status "Terdaftar"
        $('#modal_add_peserta').on('show.bs.modal', function() {
            // Find option with text "Terdaftar" (case insensitive) and select it
             $("#status_id_add option").filter(function() {
                return $(this).text().trim().toLowerCase() === 'terdaftar'; 
            }).prop('selected', true).trigger('change');
        });

        // Autocomplete setup
        $("#search_add").autocomplete({
            source: function(request, response) {
                $.ajax({
                    type: "get",
                    data: {
                        term: request.term
                    },
                    url: "{{ route('operasi.peserta.autocomplete') }}",
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                var valItem = ui.item.value;

                valItem = valItem.split('-');
                valItem = valItem[0].substr(0, valItem[0].length - 1);
                $('#search_add').val(valItem);
                document.getElementById('search_btn_add').click();
                return false; 
            }
        });

        // Form Submission
        $('#form_add_peserta').submit(function(e) {
            e.preventDefault();
            var url = "{{ route('admin.peserta.add') }}";
            var fd = new FormData($(this)[0]);

            $.ajax({
                type: "post",
                url: url,
                data: fd,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#btn_simpan_peserta').attr('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                },
                success: function(response) {
                    $('#modal_add_peserta').modal('hide');
                    swalToast(200, response.data);
                    cardRefresh(); // Refresh table
                    $('#form_add_peserta')[0].reset(); // Reset form
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    var msg = 'Gagal menyimpan data';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.data || xhr.responseJSON.message;
                    }
                     swalToast(500, msg);
                },
                complete: function() {
                    $('#btn_simpan_peserta').attr('disabled', false).html('Simpan');
                }
            });
        });
    });

    function getDataMahasiswa() {
        let search = $('#search_add').val();
        if(!search) return;

        $.ajax({
            type: "POST",
            url: "{{ route('operasi.peserta.getData') }}",
            data: {
                _token: "{{ csrf_token() }}",
                search: search
            },
            beforeSend: function() {
                // Optional: Show loading overlay on modal
            },
            success: function(response) {
                console.log(response);
                if(response.status) {
                    let mhs = response.data;
                    $('#nim_add').val(mhs.nim);
                    $('#nama_add').val(mhs.nama);
                    
                    // Attempt to auto-select prodi if name matches
                    if(mhs.prodi && mhs.prodi.nama) {
                         // Find option with text matching prodi name
                         $("#prodi_id_add option").filter(function() {
                            return $(this).text() == mhs.prodi.nama; 
                        }).prop('selected', true).trigger('change');
                    }
                    
                    // Robust logic for gender
                    let jk = mhs.jk.nama;
 
                    if(jk) {
                         jk = jk.toString().toUpperCase();
                         if(jk === 'L' || jk === 'LAKI-LAKI' || jk === '1') {
                             $('#jenis_kelamin_add').val('Laki-laki').trigger('change');
                         } else if (jk === 'P' || jk === 'PEREMPUAN' || jk === '2') {
                             $('#jenis_kelamin_add').val('Perempuan').trigger('change');
                         }
                    }

                    // Optional fields if available
                    $('#nik_add').val(mhs.nik || '');
                    $('#tempat_lahir_add').val(mhs.tempat_lahir || '');
                    $('#tanggal_lahir_add').val(mhs.tanggal_lahir || '');
                    $('#nomor_hp_add').val(mhs.hp || '');
                    $('#alamat_add').val(mhs.alamat || '');

                    swalToast(200, "Data ditemukan!");
                } else {
                    swalToast(500, "Data tidak ditemukan di Master Mahasiswa, silakan isi manual.");
                }
            },
            error: function() {
                 swalToast(500, "Gagal mengambil data.");
            }
        });
    }
</script>
@endpush
