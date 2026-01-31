<div class="row">
    <div class="col-12">
        <div class="card card-outline card-success">
            <button type="button" class="btn btn-tool d-none" id="card_refresh_info" data-card-widget="card-refresh"
                data-source="{{ url()->current() }}" data-source-selector="#card_refresh_content_info"
                data-load-on-init="false">
                <i class="fas fa-sync-alt"></i>
            </button>
            <div class="card-body" id="card_refresh_content_info">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th scope="row">NIM</th>
                            <td id="posko">{{ @$peserta->nim }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Nama</th>
                            <td>{{ @$peserta->nama }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Nilai</th>
                            <td><b>{{ $nilai->nilai ?? 0 }}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
