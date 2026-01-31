<!-- Profile Image -->
<div class="card card-primary card-outline">
    <button type="button" class="btn btn-tool d-none" id="card_refresh_left_side_acc" data-card-widget="card-refresh"
        data-source="{{ url()->current() }}" data-source-selector="#card-refresh-content-left-side-acc"
        data-load-on-init="false">
        <i class="fas fa-sync-alt"></i>
    </button>
    <div class="card-body box-profile" id="card-refresh-content-left-side-acc">
        <div class="text-center">
            @if (@$foto->path)
                <img src="{{ \App\Http\Services\GoogleDrive::showImage($foto->path) }}"
                    style="width: 100px;height:100px;object-fit: cover" class="profile-user-img img-fluid img-circle"
                    alt="User Image">
            @else
                <img src="{{ asset('/img/logo uii dalwa.png') }}" class="profile-user-img img-fluid img-circle"
                    alt="User Image">
            @endif
        </div>

        <h3 class="profile-username text-center">{{ @$peserta->nama }}</h3>
        <div class="text-center"><span
                class="badge badge-{{ @$peserta->status->warna }}">{{ @$peserta->status->nama }}</span>
        </div>
        <ul class="list-group list-group-unbordered my-3">
            <li class="list-group-item">
                <b>Jenis Kelamin</b> <a class="float-right">{{ @$peserta->user->jenis_kelamin }}</a>
            </li>

            <li class="list-group-item">
                <b>Prodi</b> <a class="float-right">{{ @$peserta->prodi->alias }}</a>
            </li>

            <li class="list-group-item">
                <b>Nilai</b> <a class="float-right">{!! $nilai ? $nilai->nilai : '<span class="badge badge-danger">Belum Ada Nilai</span>' !!}</a>
            </li>
            <li class="list-group-item">
                <a href="{{route('admin.penilaian.peserta.input', ['peserta' => $peserta])}}" class="btn btn-primary w-100">Detail</a>
            </li>
        </ul>
    </div>
</div>
<!-- /.card -->

@push('script')
    <script>
        $('#card_refresh_left_side_acc').on('overlay.removed.lte.cardrefresh', function() {});
    </script>
@endpush
