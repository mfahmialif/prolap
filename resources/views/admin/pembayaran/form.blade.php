{{-- 
    jenjang => "s1" or "pasca"
--}}
@php
    $cekPeserta = $jenjang == 's1' ? 'cekPesertaS1' : 'cekPesertaPasca';
    $baru = $jenjang == 's1' ? 'baruS1' : 'barupasca';
@endphp
<div class="form-group">
    <label for="search">Cari</label>
    <div class="input-group">
        <input type="input" name="search" class="form-control" id="search_{{ $jenjang }}"
            placeholder="Masukkan NIM / Nama" onfocus="this.select();">
        <button type="button" class="btn btn-primary" id="search_btn_{{ $jenjang }}" style="cursor: pointer"
            onclick="{{ $cekPeserta }}('#modal_add_pembayaran_{{ $jenjang }}', '#search_{{ $jenjang }}','#nim_{{ $jenjang }}', '#nama_{{ $jenjang }}','#jenis_kelamin_{{ $jenjang }}',
            '#prodi_{{ $jenjang }}', '#jenis_{{ $jenjang }}','#tahun_{{ $jenjang }}', '#jenis_pembayaran_{{ $jenjang }}', '#jumlah_{{ $jenjang }}', '#kamar_{{ $jenjang }}', '#ukuran_baju_{{ $jenjang }}','#mursal_{{ $jenjang }}', '#keterangan_{{ $jenjang }}', '#form_submit_{{ $jenjang }}' )" />
        <i class="fa fa-search"></i>
        </button>
        <button type="button" class="btn btn-success" id="search_btn_{{ $jenjang }}"
            style="cursor: pointer;margin-left: 5px"
            onclick="{{ $baru }}('#modal_add_pembayaran_{{ $jenjang }}', '#search_{{ $jenjang }}','#nim_{{ $jenjang }}', '#nama_{{ $jenjang }}','#jenis_kelamin_{{ $jenjang }}',
            '#prodi_{{ $jenjang }}', '#jenis_{{ $jenjang }}', '#tahun_{{ $jenjang }}', '#jenis_pembayaran_{{ $jenjang }}', '#jumlah_{{ $jenjang }}', '#kamar_{{ $jenjang }}', '#ukuran_baju_{{ $jenjang }}','#mursal_{{ $jenjang }}', '#keterangan_{{ $jenjang }}', '#form_submit_{{ $jenjang }}' )" />
        Baru
        </button>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="nim_{{ $jenjang }}">NIM</label>
            <input type="input" name="nim" class="form-control" id="nim_{{ $jenjang }}" readonly required
                placeholder="Masukkan NIM">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="nama_{{ $jenjang }}">Nama</label>
            <input type="input" name="nama" class="form-control" id="nama_{{ $jenjang }}" disabled required
                placeholder="Masukkan Nama">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="jenis_kelamin_{{ $jenjang }}">Jenis Kelamin</label>
            <input type="hidden" id="jenis_kelamin_{{ $jenjang }}_hidden" name="jenis_kelamin" readonly>
            <select class="form-control select2bs4 w-100" id="jenis_kelamin_{{ $jenjang }}" disabled required>
                <option value="">Pilih Jenis Kelamin</option>
                @foreach (\Helper::getEnumValues('users', 'jenis_kelamin', ['*']) as $item)
                    <option value="{{ $item }}">{{ strtoupper($item) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="prodi_{{ $jenjang }}">Prodi</label>
            <input type="hidden" name="prodi_id" id="prodi_{{ $jenjang }}_hidden" readonly>
            <select class="form-control select2bs4 w-100" id="prodi_{{ $jenjang }}" disabled required>
                <option value="">Pilih Prodi</option>
                @foreach ($prodiS1 as $item)
                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="jenis_{{ $jenjang }}">Jenis <span class="text-warning">*Bisa
            dikosongi</span></label>
    <select class="form-control select2bs4 w-100" id="jenis_{{ $jenjang }}" name="jenis" disabled>
        <option value="">Pilih Jenis</option>
        @foreach (\Helper::getEnumValues('peserta', 'jenis') as $item)
            <option value="{{ $item }}">{{ $item }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="tahun_{{ $jenjang }}">Tahun</label>
    <select class="form-control select2bs4 w-100" id="tahun_{{ $jenjang }}" name="tahun_id" disabled required>
        <option value="">Pilih Tahun</option>
        @foreach ($tahun as $item)
            <option value="{{ $item->id }}">{{ $item->nama }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="kamar_{{ $jenjang }}" class="form-label">Kamar <span class="text-warning">*Bisa
            dikosongi</span></label>
    <input type="text" class="form-control" id="kamar_{{ $jenjang }}" name="kamar" placeholder="Kamar"
        disabled>
</div>
<div class="form-group">
    <label for="ukuran_baju_{{ $jenjang }}">Ukuran Baju <span class="text-warning">*Bisa
            dikosongi</span></label>
    <select class="form-control select2bs4 w-100" id="ukuran_baju_{{ $jenjang }}" name="ukuran_baju" disabled>
        <option value="">Pilih Ukuran Baju</option>
        @foreach (\Helper::getEnumValues('peserta', 'ukuran_baju') as $item)
            <option value="{{ $item }}">{{ $item }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="mursal_{{ $jenjang }}">Status Mursal <span class="text-warning">*Bisa
            dikosongi</span></label>
    <select class="form-control select2bs4 w-100" id="mursal_{{ $jenjang }}" name="mursal" disabled>
        <option value="">Pilih Status Mursal</option>
        @foreach (\Helper::getEnumValues('peserta', 'mursal') as $item)
            <option value="{{ $item }}">{{ $item }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="jumlah_{{ $jenjang }}">Jumlah</label>
    <input type="number" name="jumlah" class="form-control" id="jumlah_{{ $jenjang }}"
        placeholder="Masukkan Jumlah yang Dibayar" onkeyup="formatRupiah(this.value,'#jumlahS1Help')"
        onfocus="this.select();" disabled required autocomplete="off">
    <small id="jumlahS1Help" class="form-text text-muted">Rp.</small>
</div>
<div class="form-group">
    <label for="jenis_pembayaran_{{ $jenjang }}">Jenis Pembayaran</label>
    <select class="form-control select2bs4 w-100" id="jenis_pembayaran_{{ $jenjang }}" name="jenis_pembayaran"
        disabled required>
        <option value="">Pilih Jenis Pembayaran</option>
        @foreach (\Helper::getEnumValues('pembayaran', 'jenis_pembayaran') as $item)
            <option value="{{ $item }}">{{ $item }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="keterangan_{{ $jenjang }}">Keterangan <span class="text-warning">*Bisa
            dikosongi</span></label>
    <textarea name="keterangan" rows="3" class="form-control" id="keterangan_{{ $jenjang }}" disabled
        placeholder="Masukkan Keterangan"></textarea>
</div>
