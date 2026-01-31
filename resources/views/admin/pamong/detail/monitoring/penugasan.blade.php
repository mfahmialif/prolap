<div class="card">
    <div class="card-header">
        <b>Penugasan</b>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" id="card_refresh_penugasan" data-card-widget="card-refresh"
                data-source="{{ url()->current() }}" data-source-selector="#card_refresh_penugasan_content"
                data-load-on-init="false">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body" id="card_refresh_penugasan_content">
        @foreach ($jumlahPenugasan as $itemJumlahPenugasan)
            <b>{{ $itemJumlahPenugasan->pamong_nama }}</b>
            <hr>
            <div>Jumlah Rekapan: {{ $itemJumlahPenugasan->jumlah_tugas }}</div>
            @if ($itemJumlahPenugasan->jumlah_tugas < 1)
                <div><a href="{{ route('admin.penugasan.pamong.detail', ['idPamong' => $itemJumlahPenugasan->id]) }}"><span
                            class="text-danger"><u>Belum Mencukupi Jumlah Penugasan, Klik
                            Disini Untuk Penugasan</u></span></a></div>
            @endif
            @if (count($itemJumlahPenugasan->monitoring) != 0)
                <div>List data penugasan yang belum lengkap (PERLU DIISI): </div>
                <div class="list-group mt-3">
                    @foreach ($itemJumlahPenugasan->monitoring as $monitoring)
                        <a class="list-group-item list-group-item-action list-group-item-danger"
                            href="{{ route('admin.penugasan.pamong.detail.input', ['idPamong' => $itemJumlahPenugasan->id, 'idPenugasanPamong' => $monitoring['data']->id]) }}">
                            ({{ $monitoring['data']->penugasan }})
                            | {{ $monitoring['totalPenugasan'] . ' dari ' . $monitoring['jumlahPeserta'] }} =>
                            Klik
                            Untuk Isi Penugasan
                        </a>
                    @endforeach
                </div>
            @endif
            <hr>
        @endforeach
    </div>
</div>
