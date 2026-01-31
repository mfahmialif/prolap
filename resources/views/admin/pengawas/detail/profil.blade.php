<!-- Profile Image -->
<div class="card card-primary card-outline">
    <button type="button" class="btn btn-tool d-none" id="card_refresh_profil_acc" data-card-widget="card-refresh"
        data-source="{{ url()->current() }}" data-source-selector="#card-refresh-content-profil" data-load-on-init="false">
        <i class="fas fa-sync-alt"></i>
    </button>
    <div class="card-body box-profile" id="card-refresh-content-profil">
        <div class="text-center">
            @if (@$pengawas->user->foto)
                <img src="{{ \App\Http\Services\GoogleDrive::showImage(@$pengawas->user->foto) }}"
                    style="width: 100px;height:100px;object-fit: cover" class="profile-user-img img-fluid img-circle"
                    alt="User Image">
            @else
                <img src="{{ asset('/img/logo uii dalwa.png') }}" class="profile-user-img img-fluid img-circle"
                    alt="User Image">
            @endif
        </div>

        <h3 class="profile-username text-center">{{ @$pengawas->nama }}</h3>
        <div class="text-center"><span
                class="badge badge-{{ \Helper::getColor($pengawas->status) }}">{{ @$pengawas->status }}</span></div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer bg-primary text-center">
        {{ @$pengawas->nama }}
    </div>
</div>
<!-- /.card -->

<!-- About Me Box -->
<div class="card card-primary collapsed-card mt-3">
    <div class="card-header">
        <h3 class="card-title">Detail</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                <i class="fas fa-expand"></i>
            </button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <strong>Username</strong>
        <p class="text-muted">{{ @$pengawas->user->username }}</p>
        <hr>
        <strong>Password</strong>
        <p class="text-muted">{{ @$pengawas->user->no_unik }}</p>
        <hr>
        <strong>Nama</strong>
        <p class="text-muted">{{ @$pengawas->nama }}</p>
        <hr>
        <strong>Email</strong>
        <p class="text-muted">{{ @$pengawas->user->email }}</p>
        <hr>
        <strong>HP</strong>
        <p class="text-muted">{{ @$pengawas->hp }}</p>
        <hr>
        <strong>Jenis Kelamin</strong>
        <p class="text-muted">{{ @$pengawas->user->jenis_kelamin }}</p>
        <hr>

    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
