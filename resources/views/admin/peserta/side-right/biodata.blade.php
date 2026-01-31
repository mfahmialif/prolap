@push('css')
<style>
    .biodata input[readonly],
    .biodata textarea[readonly] {
        background-color: #f8f9fa !important;
    }

    .biodata .select2-container--disabled .select2-selection__rendered {
        background-color: #f8f9fa !important;
    }
</style>
@endpush
<form class="form-horizontal biodata" id="form-biodata" method="POST" action="#"">
    @csrf
    @method('PUT')
    <div class=" form-group row">
    <label for="username" class="col-sm-3 col-form-label">Username</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="username" name="username" value="{{ @$peserta->user->username }}"
            readonly>
    </div>
    </div>
    <div class="form-group row">
        <label for="nim" class="col-sm-3 col-form-label">NIM</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="nim" name="nim" value="{{ $peserta->nim }}" readonly>
        </div>
    </div>
    <div class="form-group row">
        <label for="nik" class="col-sm-3 col-form-label">NIK</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="nik" name="nik" value="{{ $peserta->nik }}" readonly>
        </div>
    </div>
    <div class="form-group row">
        <label for="nama" class="col-sm-3 col-form-label">Nama</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="nama" name="nama" value="{{ $peserta->nama }}" readonly>
        </div>
    </div>
    <div class="form-group row">
        <label for="nama_pondok" class="col-sm-3 col-form-label">Nama Pondok</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="nama_pondok" name="nama_pondok"
                value="{{ $peserta->nama_pondok }}" readonly>
        </div>
    </div>
    <div class="form-group row">
        <label for="prodi_id" class="col-sm-3 col-form-label">Prodi</label>
        <div class="col-sm-9">
            <select class="form-control select2bs4 w-100" id="prodi_id" name="prodi_id" disabled>
                @foreach ($prodi as $item)
                <option value="{{ $item->id }}" {{ $item->id == $peserta->prodi_id ? 'selected' : '' }}>
                    {{ strtoupper($item->nama) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label for="jenis_kelamin" class="col-sm-3 col-form-label">Jenis Kelamin</label>
        <div class="col-sm-9">
            <select class="form-control select2bs4 w-100" id="jenis_kelamin" name="jenis_kelamin" disabled>
                @foreach (BulkData::jenisKelamin as $item)
                <option value="{{ $item }}" {{ $item==$peserta->user->jenis_kelamin ? 'selected' : '' }}>
                    {{ strtoupper($item) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label for="tanggal_daftar" class="col-sm-3 col-form-label">Tanggal Daftar</label>
        <div class="col-sm-9">
            <input type="date" class="form-control" id="tanggal_daftar" name="tanggal_daftar"
                value="{{ $peserta->tanggal_daftar }}" readonly>
        </div>
    </div>
    <div class="form-group row">
        <label for="tempat_lahir" class="col-sm-3 col-form-label">Tempat Lahir</label>
        <div class="col-sm-3">
            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir"
                value="{{ $peserta->tempat_lahir }}" readonly>
        </div>
        <label for="tanggal_lahir" class="col-sm-3 col-form-label">Tanggal Lahir</label>
        <div class="col-sm-3">
            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                value="{{ $peserta->tanggal_lahir }}" readonly />
        </div>
    </div>
    <div class="form-group row">
        <label for="jenis" class="col-sm-3 col-form-label">Jenis</label>
        <div class="col-sm-9">
            <select class="form-control select2bs4 w-100" id="jenis" name="jenis" disabled>
                <option value="">PILIH JENIS</option>
                @foreach (\Helper::getEnumValues('peserta', 'jenis') as $item)
                <option value="{{ $item }}" {{ $item==$peserta->jenis ? 'selected' : '' }}>
                    {{ strtoupper($item) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label for="tahun_id" class="col-sm-3 col-form-label">Tahun</label>
        <div class="col-sm-9">
            <select class="form-control select2bs4 w-100" id="tahun_id" name="tahun_id" disabled>
                <option value="">PILIH TAHUN</option>
                @foreach ($tahun as $item)
                <option value="{{ $item->id }}" {{ $item->id == $peserta->tahun_id ? 'selected' : '' }}>
                    {{ strtoupper($item->nama) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label for="nomor_hp" class="col-sm-3 col-form-label">Nomor HP/WA</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="nomor_hp" name="nomor_hp" value="{{ $peserta->nomor_hp }}"
                readonly>
        </div>
    </div>
    <div class="form-group row">
        <label for="nomor_hp_orang_tua" class="col-sm-3 col-form-label">Nomor HP/WA Orang Tua</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="nomor_hp_orang_tua" name="nomor_hp_orang_tua"
                value="{{ $peserta->nomor_hp_orang_tua }}" readonly>
        </div>
    </div>
    <div class="form-group row">
        <label for="alamat" class="col-sm-3 col-form-label">Alamat</label>
        <div class="col-sm-9">
            <textarea class="form-control" name="alamat" id="alamat" rows="3" readonly>{{ $peserta->alamat }}</textarea>
        </div>
    </div>
    <div class="form-group row">
        <label for="kamar" class="col-sm-3 col-form-label">Kamar</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="kamar" name="kamar" value="{{ $peserta->kamar }}" readonly>
        </div>
    </div>
    <div class="form-group row">
        <label for="kelas_pondok" class="col-sm-3 col-form-label">Kelas Pondok</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="kelas_pondok" name="kelas_pondok"
                value="{{ $peserta->kelas_pondok }}" readonly>
        </div>
    </div>
    <div class="form-group row">
        <label for="qism_pondok" class="col-sm-3 col-form-label">Qism Pondok</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="qism_pondok" name="qism_pondok"
                value="{{ $peserta->qism_pondok }}" readonly>
        </div>
    </div>
    <div class="form-group row">
        <label for="keahlian" class="col-sm-3 col-form-label">Keahlian</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="keahlian" name="keahlian" value="{{ $peserta->keahlian }}"
                readonly>
        </div>
    </div>
    <div class="form-group row">
        <label for="mahir_bahasa_lokal" class="col-sm-3 col-form-label">Mahir Bahasa Lokal</label>
        <div class="col-sm-9">
            <select class="form-control select2bs4 w-100" id="mahir_bahasa_lokal" name="mahir_bahasa_lokal" disabled>
                <option value="">PILIH MAHIR BAHASA LOKAL</option>
                @foreach (\Helper::getEnumValues('peserta', 'mahir_bahasa_lokal') as $item)
                <option value="{{ $item }}" {{ $item==$peserta->mahir_bahasa_lokal ? 'selected' : '' }}>
                    {{ strtoupper($item) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label for="ukuran_baju" class="col-sm-3 col-form-label">Ukuran Baju</label>
        <div class="col-sm-9">
            <select class="form-control select2bs4 w-100" id="ukuran_baju" name="ukuran_baju" disabled>
                <option value="">PILIH UKURAN BAJU</option>
                @foreach (\Helper::getEnumValues('peserta', 'ukuran_baju') as $item)
                <option value="{{ $item }}" {{ $item==$peserta->ukuran_baju ? 'selected' : '' }}>
                    {{ strtoupper($item) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label for="mursal" class="col-sm-3 col-form-label">Mursal</label>
        <div class="col-sm-9">
            <select class="form-control select2bs4 w-100" id="mursal" name="mursal" disabled>
                <option value="">PILIH STATUS MURSAL</option>
                @foreach (\Helper::getEnumValues('peserta', 'mursal') as $item)
                <option value="{{ $item }}" {{ $item==$peserta->mursal ? 'selected' : '' }}>
                    {{ strtoupper($item) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label for="status_id" class="col-sm-3 col-form-label">Status</label>
        <div class="col-sm-9">
            <select class="form-control select2bs4 w-100" id="status_id" name="status_id" disabled>
                @foreach ($status as $item)
                <option value="{{ $item->id }}" {{ $item->id == $peserta->status_id ? 'selected' : '' }}>
                    {{ strtoupper($item->nama) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label for="keterangan" class="col-sm-3 col-form-label">Keterangan</label>
        <div class="col-sm-9">
            <textarea class="form-control" name="keterangan" id="keterangan" rows="3"
                readonly>{{ $peserta->keterangan }}</textarea>
        </div>
    </div>
    <div class="form-group row">
        <div class="offset-sm-3 col-sm-9">
            <button type="button" class="btn btn-primary mr-3" id="button_edit_biodata">Edit Data</button>
            <button type="button" class="btn btn-danger mr-3 d-none" id="button_cancel_biodata">Batal</button>
            <button type="submit" class="btn btn-success mr-3 d-none" id="button_submit_biodata">Simpan</button>
        </div>
    </div>
</form>

@push('script')
<script>
    initBiodata();

        function initBiodata() {

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            $('#button_edit_biodata').click(function(e) {
                $('#button_edit_biodata').prop('disabled', true);
                $('.biodata input[readonly]').removeAttr('readonly');
                $('.biodata textarea[readonly]').removeAttr('readonly');
                $('.biodata .select2bs4').prop('disabled', false).trigger('change');
                $('#button_cancel_biodata').removeClass("d-none");
                $('#button_submit_biodata').removeClass("d-none");
            });

            $('#button_cancel_biodata').click(function(e) {
                $('#button_edit_biodata').prop('disabled', false);
                $('.biodata input').prop('readonly', true);
                $('.biodata textarea').prop('readonly', true);
                $('.biodata .select2bs4').prop('disabled', true).trigger('change');
                $('#button_cancel_biodata').addClass("d-none");
                $('#button_submit_biodata').addClass("d-none");
            });

            $('#form-biodata').submit(function(e) {
                // e.preventDefault();
                let html = `<div class="d-flex align-items-center">
                        <strong>Proses..</strong>
                        <div class="spinner-border spinner-border-sm ml-auto" role="status_id" aria-hidden="true"></div>
                        </div>`;
                $('#button_cancel_biodata').prop("disabled", true);
                $('#button_submit_biodata').prop("disabled", true);
                $('#button_submit_biodata').html(html);
                // this.submit();
                e.preventDefault();
                let fd = new FormData(this);
                $.ajax({
                    type: "POST",
                    url: "{{ url()->current() }}",
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        swalToast(response.message, response.data);
                        cardRefresh();
                        saveStateTab('#nav_biodata');
                    }
                });
            });

        }
</script>
@endpush