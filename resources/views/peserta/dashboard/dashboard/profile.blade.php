<div class="card card-primary card-outline">
    <div class="card-body box-profile">
        <div class="text-center mb-3 d-flex justify-content-center">
            @if (@$foto->file != null)
                <div class="profile-picture-container">
                    <img class="profile-user-img img-fluid img-circle" src="{{ $showFoto }}"
                        alt="User profile picture">
                </div>
            @else
                <div class="profile-picture-container">
                    <img class="profile-user-img img-fluid img-circle"
                        src="{{ asset('/lte4/dist/img/user.png') }}" alt="User profile picture">
                </div>
            @endif
        </div>
        <h3 class="profile-username text-center">{{ @$peserta->nama }}</h3>
        <button class="btn btn-sm btn-link text-danger w-100" data-toggle="modal"
            data-target="#modal-password"><i class="fas fa-pen"></i>
            Ubah Password</button>
        <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
                <b>Username</b> <a class="float-right">{{ @$peserta->user->username }}</a>
            </li>
            <li class="list-group-item">
                <b>Tanggal Daftar</b> <a
                    class="float-right">{{ date('d M Y', strtotime(@$peserta->tanggal_daftar)) }}</a>
            </li>
            <li class="list-group-item">
                <b>Prodi</b> <a class="float-right">{{ @$peserta->prodi->alias }}</a>
            </li>
            <li class="list-group-item">
                <b>Jenis</b> <a class="float-right">{{ @$peserta->jenis }}</a>
            </li>
        </ul>
        <a href="{{ route('peserta.formulir.edit') }}" class="btn btn-primary btn-block"><b><i
                    class="fas fa-edit mr-1"></i> Edit
                Formulir</b></a>
        <a href="{{ route('admin.nilai.detail', ['peserta' => $peserta]) }}"
            class="btn btn-info btn-block"><b><i class="fas fa-edit mr-1"></i> Lihat Nilai</b></a>
        {{-- <a href="{{ route('peserta.formulir.cetak', ['idPeserta' => @$peserta->id, 'noUnik' => @$peserta->user->no_unik]) }}"
            class="btn btn-secondary btn-block" target="_blank"><b><i class="fas fa-print mr-1"></i>
                Cetak
                Formulir</b></a> --}}
    </div>

</div>