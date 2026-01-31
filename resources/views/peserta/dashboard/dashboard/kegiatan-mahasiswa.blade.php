<div class="card card-primary card-outline card-tabs">
    <div class="card-header p-0 pt-1 border-bottom-0">
        <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
            @for ($i = 0; $i < count($peserta->poskoPeserta); $i++)
                @php
                    $poskoPeserta = $peserta->poskoPeserta[$i];
                    $posko = $poskoPeserta->posko;
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $i === 0 ? 'active' : '' }}" id="tab-posko-{{ $posko->id }}"
                        data-toggle="pill" href="#content-tab-posko-{{ $posko->id }}" role="tab"
                        aria-controls="custom-tabs-three-home" aria-selected="true">Kegiatan Mahasiswa
                        {{ $posko->nama }}</a>
                </li>
            @endfor
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="custom-tabs-three-tabContent">
            @for ($i = 0; $i < count($peserta->poskoPeserta); $i++)
                @php
                    $poskoPeserta = $peserta->poskoPeserta[$i];
                    $posko = $poskoPeserta->posko;
                @endphp
                <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}"
                    id="content-tab-posko-{{ $posko->id }}" role="tabpanel"
                    aria-labelledby="custom-tabs-three-home-tab">
                    @include('peserta.dashboard.dashboard.kegiatan-mahasiswa.tabel', [
                        'id' => $posko->id,
                        'posko' => $posko,
                    ])
                </div>
            @endfor
        </div>
    </div>
    <!-- /.card -->
</div>


@push('css')
    <style>
        .dataTables_filter {
            float: inline-end;
        }

        .dataTables_paginate.paging_simple_numbers .pagination {
            float: inline-end;
        }
    </style>
@endpush
