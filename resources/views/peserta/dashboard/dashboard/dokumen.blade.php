<div class="card card-primary card-outline">
    <div class="card-header">
        DOKUMEN
        <div class="card-tools">
            <button type="button" class="btn btn-tool" id="card_refresh"
                data-card-widget="card-refresh" data-source="{{ url()->current() }}"
                data-source-selector="#card_refresh_nidn" data-load-on-init="false">
                <i class="fas fa-sync-alt"></i>
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
    <div class="card-body" id="card_refresh_nidn">
        <p>*Upload Berkas maksimal 20MB dan format jpg/jpeg/png</p>
        @foreach ($listDokumen as $row)
            @php
                $dokumenPeserta = App\Models\PesertaDokumen::where('peserta_id', $peserta->id)
                    ->where('list_dokumen_id', $row->id)
                    ->first();
            @endphp
            <div class="py-2 p-3 rounded  border border-dashed shadow-sm mb-2">
                <div class="row">
                    <div class="col-md-6 d-flex align-items-center">
                        @if ($dokumenPeserta)
                            <span class="bg-success p-2 rounded">
                                <i class="fas fa-folder-open text-warning"></i>
                            </span>
                        @else
                            <span class="bg-danger p-2 rounded">
                                <i class="fas fa-folder-open text-warning"></i>
                            </span>
                        @endif
                        <div class="ml-3">
                            <h5 class="mb-0 pb-0">{{ strtoupper($row->tipe) }}</h5>
                            @if ($dokumenPeserta)
                                @php
                                    $linkDokumen = App\Http\Services\GoogleDrive::link(
                                        @$dokumenPeserta->path,
                                    );
                                @endphp
                                <small class="fw-bold text-success">Sudah Upload</small>
                                - <a href="{{ $linkDokumen }}"
                                    class="text-secondary text-decoration-none" target="_blank"><u>Lihat
                                        Berkas
                                        <i
                                            class="fas
                                            fa-external-link-alt"></i></u></a>
                                <br>
                                <small><span class="text-danger">*</span>Silahkan upload ulang jika
                                    ingin
                                    mengubah
                                </small>
                            @else
                                @if ($row->status == 'wajib')
                                    <small class="fw-bold text-danger">*Wajib</small>
                                @endif
                                @if ($row->status == 'opsional')
                                    <small class="fw-bold text-warning">*Opsional</small>
                                @endif
                                <small class="fw-bold text-danger">| Belum Upload</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>