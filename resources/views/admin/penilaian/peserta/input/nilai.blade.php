@php
    $tipeDb = $tipe;
    $title = strtoupper($tipe);
    $tipe = $tipe . '_' . $id;
@endphp
{{-- Hidden --}}
<input type="hidden" id="peserta_id_{{ $tipe }}" name="peserta_id" value="{{ $peserta->id }}">
<input type="hidden" id="supervisor_id_{{ $tipe }}" name="supervisor_id" value="{{ $jenisId }}">
<input type="hidden" name="tipe_db" value="{{ $tipeDb }}">
<input type="hidden" name="tipe" value="{{ $tipe }}">
{{-- End Hidden --}}

<div class="row">
    <div class="col-12">
        <div class="card card-outline card-secondary" id="card_{{ $tipe }}">
            <div class="card-header">
                <strong>{{ strtoupper($title) }}</strong> - {{ $supervisor->nama }}
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" id="card_refresh_{{ $tipe }}"
                        data-card-widget="card-refresh" data-source="{{ url()->current() }}"
                        data-source-selector="#card_refresh_content_{{ $tipe }}" data-load-on-init="false">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="maximize">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body" id="card_refresh_content_{{ $tipe }}">
                <div class="row">
                    @php
                        $nilaiAkhir = 0;
                    @endphp
                    @foreach ($komponenNilai->where('jenis', $tipeDb)->where('tahun_id', $tahunId) as $item)
                        @php
                            $nilai = \DB::table('penilaian_' . $tipeDb)
                                ->join(
                                    'penilaian_' . $tipeDb . '_detail',
                                    'penilaian_' . $tipeDb . '_detail.penilaian_' . $tipeDb . '_id',
                                    '=',
                                    'penilaian_' . $tipeDb . '.id',
                                )
                                ->where('penilaian_' . $tipeDb . '_detail.komponen_nilai_id', $item->id)
                                ->where('penilaian_' . $tipeDb . '.' . $jenis . '_id', $jenisId)
                                ->where('penilaian_' . $tipeDb . '.' . $tipePeserta . '_peserta_id', $peserta->id)
                                ->select('penilaian_' . $tipeDb . '_detail.nilai')
                                ->first();
                            $nilai = $nilai ? $nilai->nilai : 0;
                            $nilaiAkhir += ($nilai * $item->bobot) / 100;
                        @endphp
                        <div class="col-12 col-md">
                            <div class="form-group">
                                <label
                                    for="nilai_{{ $item->nama }}_{{ $tipe }}">{{ strtoupper($item->nama) }}
                                    ({{ $item->bobot }}%)
                                </label>
                                <input type="number" class="form-control text-center"
                                    id="nilai_{{ $item->nama }}_{{ $tipe }}"
                                    name="nilai_{{ $item->nama }}"
                                    onkeyup="setNilaiAkhir('{{ $tipe }}', event)" value="{{ $nilai }}"
                                    {{ $disabled ? 'disabled': '' }}
                                    {{ strtolower($item->nama) == 'absensi' ? ' readonly tabindex="-1"' : '' }}>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-12 col-md">
                        <div class="form-group">
                            <label for="nilai_akhir_{{ $tipe }}">NILAI AKHIR</label>
                            <input type="number" class="form-control text-center" id="nilai_akhir_{{ $tipe }}"
                                name="nilai_akhir" step="any" value="{{ $nilaiAkhir }}" readonly tabindex="-1">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
