@php
    $param = isset($param) ? "_$param" : '';
    $cardId = $cardId ?? '';
    $posko = $posko ?? false;
@endphp
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="nama{{ $param }}">Nama</label>
            <input type="text" name="nama" id="nama{{ $param }}" class="form-control"
                placeholder="Masukkan Nama Pengawas" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="jenis_kelamin{{ $param }}">Jenis Kelamin</label>
            <select class="form-control select2bs4 w-100" name="jenis_kelamin" id="jenis_kelamin{{ $param }}"
                required>
                <option value="">Pilih Jenis Kelamin</option>
                @foreach (\Helper::getEnumValues('users', 'jenis_kelamin') as $item)
                    <option value="{{ $item }}">{{ $item }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="email{{ $param }}">Email <span class="text-warning">*Bisa
                    dikosongi</span></label>
            <input type="email" name="email" id="email{{ $param }}" class="form-control"
                placeholder="Masukkan Email Pengawas">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="hp{{ $param }}">HP <span class="text-warning">*Bisa
                    dikosongi</span></label>
            <input type="text" name="hp" id="hp{{ $param }}" class="form-control"
                placeholder="Masukkan Nomor HP Pengawas">
        </div>
    </div>
</div>
