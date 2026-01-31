<div class="card">
    <div class="card-header">
        <b>Penilaian</b>
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
        <b>{{ $pamong->nama }}</b><br>
        <small>*Klik data di bawah untuk menuju halaman input nilai sesuai posko PPL/PKL</small>
        <hr>
        <ul class="list-group">
            @foreach ($pamong->penilaian as $itemPenilaian)
                @if ($itemPenilaian->nilai)
                    <a href="{{ route('admin.penilaian.pamong.input', ['pamong' => $pamong]) }}" class="list-group-item list-group-item-action list-group-item-success">{{ $itemPenilaian->peserta->nama }}
                            ({{ $itemPenilaian->peserta->nim }})
                            | {{ $itemPenilaian->peserta->prodi->alias }} => {{ $itemPenilaian->nilai }}</a>
                @else
                    <a href="{{ route('admin.penilaian.pamong.input', ['pamong' => $pamong]) }}" class="list-group-item list-group-item-action list-group-item-danger">{{ $itemPenilaian->peserta->nama }}
                            ({{ $itemPenilaian->peserta->nim }})
                            | {{ $itemPenilaian->peserta->prodi->alias }} => 0</a>
                @endif
            @endforeach
        </ul>
        <hr>
    </div>
</div>
