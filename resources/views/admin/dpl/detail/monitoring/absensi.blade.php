<div class="card">
    <div class="card-header">
        <b>Absensi</b>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" id="card_refresh" data-card-widget="card-refresh"
                data-source="{{ url()->current() }}" data-source-selector="#card_refresh_absensi_content"
                data-load-on-init="false">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body" id="card_refresh_absensi_content">
        @foreach ($jumlahAbsensi as $itemJumlahAbsensi)
            @php
                $tahun = \App\Models\Posko::find($itemJumlahAbsensi->posko_id)->tahun->nama;
            @endphp
            <b>{{ $itemJumlahAbsensi->posko_nama }} - {{ $tahun }}</b>
            <hr>
            <div>Jumlah Rekapan: {{ $itemJumlahAbsensi->jumlah_absensi }}</div>
            @if ($itemJumlahAbsensi->jumlah_absensi < 2)
                <div><a href="{{ route('admin.absensi.detail', ['idPoskoDpl' => $itemJumlahAbsensi->posko_dpl_id]) }}"><span
                            class="text-danger"><u>Belum Mencukupi Jumlah Absensi, Klik
                            Disini Untuk Absensi</u></span></a></div>
            @endif
            @if (count($itemJumlahAbsensi->monitoring) != 0)
                <div>List data absensi yang belum lengkap (PERLU DIISI): </div>
                <div class="list-group mt-3">
                    @foreach ($itemJumlahAbsensi->monitoring as $monitoring)
                        <a class="list-group-item list-group-item-action list-group-item-danger"
                            href="{{ route('admin.absensi.detail.edit.form', ['idPoskoDpl' => $itemJumlahAbsensi->posko_dpl_id, 'idAbsensiPsDpl' => $monitoring['data']->id]) }}">
                            ({{ $monitoring['data']->nama }})
                            | {{ $monitoring['totalAbsensi'] . ' dari ' . $monitoring['jumlahPeserta'] }} =>
                            Klik
                            Untuk ABSENSI
                        </a>
                    @endforeach
                </div>
            @endif
            <hr>
        @endforeach
    </div>
</div>
