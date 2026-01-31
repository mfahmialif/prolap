<!-- List -->
<div class="card card-primary card-outline">
    <div class="card-header">
        <div class="card-tools">
            <button type="button" class="btn btn-tool" id="card_refresh_list" data-card-widget="card-refresh"
                data-source="{{ url()->current() }}" data-source-selector="#card-refresh-content-list"
                data-load-on-init="false">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div><!-- /.card-header -->
    <div class="card-body" id="card-refresh-content-list">
        <div class="timeline">
            <div>
                <i class="fas fa-home bg-blue"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fas fa-check"></i></span>
                    <h3 class="timeline-header"><a href="#">Posko</a> Informasi Posko</h3>
                    <div class="timeline-body">
                        Silahkan klik tombol Posko untuk menuju informasi Posko
                    </div>
                    <div class="timeline-footer">
                        <a href="{{ route('admin.dpl.detailPosko', ['dpl' => $dpl]) }}"
                            class="btn btn-primary btn-sm">Posko</a>
                    </div>
                </div>
            </div>
            <div>
                <i class="fas fa-check-square bg-success"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fas fa-check"></i></span>
                    <h3 class="timeline-header"><a href="#">Absensi</a> Informasi Absensi</h3>
                    <div class="timeline-body">
                        Silahkan klik tombol Absensi untuk menuju informasi Absensi
                    </div>
                    <div class="timeline-footer">
                        <a href="{{ route('admin.dpl.detailAbsensi', ['dpl' => $dpl]) }}"
                            class="btn btn-primary btn-sm">Absensi</a>
                    </div>
                </div>
            </div>
            <div>
                <i class="fas fa-users bg-info"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fas fa-check"></i></span>
                    <h3 class="timeline-header"><a href="#">Kegiatan Kelompok Mahasiswa</a> Informasi Kegiatan Kelompok Mahasiswa</h3>
                    <div class="timeline-body">
                        Silahkan klik tombol Kegiatan Kelompok Mahasiswa untuk menuju informasi Kegiatan Kelompok Mahasiswa
                    </div>
                    <div class="timeline-footer">
                        <a href="{{ route('admin.dpl.detailKegiatanMahasiswa', ['dpl' => $dpl]) }}"
                            class="btn btn-primary btn-sm">Kegiatan Kelompok Mahasiswa</a>
                    </div>
                </div>
            </div>
            <div>
                <i class="fas fa-tasks bg-warning"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fas fa-check"></i></span>
                    <h3 class="timeline-header"><a href="#">Penugasan</a> Informasi Penugasan</h3>
                    <div class="timeline-body">
                        Silahkan klik tombol Penugasan untuk menuju informasi Penugasan
                    </div>
                    <div class="timeline-footer">
                        <a href="{{ route('admin.dpl.detailPenugasan', ['dpl' => $dpl]) }}"
                            class="btn btn-primary btn-sm">Penugasan</a>
                    </div>
                </div>
            </div>
            <div>
                <i class="fas fa-file bg-red"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fas fa-check"></i></span>
                    <h3 class="timeline-header"><a href="#">Dokumen Wajib</a> Sebelum dan Sesudah Mengisi Nilai</h3>
                    <div class="timeline-body">
                        Silahkan klik tombol Dokumen Wajib Sebelum Mengisi Nilai untuk mengisi <b>Rubrik Penilaian, Berita Acara, dan Foto Dokumentasi</b> dan <b>Video</b> Sesudah Mengisi Nilai
                    </div>
                    <div class="timeline-footer">
                        <a href="{{ route('admin.dpl.detailDokumenWajib', ['dpl' => $dpl]) }}"
                            class="btn btn-primary btn-sm">Dokumen Wajib Sebelum dan Sesudah Mengisi Nilai</a>
                    </div>
                </div>
            </div>
            <div>
                <i class="fas fa-gavel bg-info"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fas fa-check"></i></span>
                    <h3 class="timeline-header"><a href="#">Penilaian</a> Informasi Penilaian</h3>
                    <div class="timeline-body">
                        Silahkan klik tombol Penilaian untuk menuju informasi Penilaian
                    </div>
                    <div class="timeline-footer">
                        <a href="{{ route('admin.dpl.detailPenilaian', ['dpl' => $dpl]) }}"
                            class="btn btn-primary btn-sm">Penilaian</a>
                    </div>
                </div>
            </div>
            <div>
                <i class="fas fa-desktop bg-danger"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fas fa-check"></i></span>
                    <h3 class="timeline-header"><a href="#">Monitoring</a> Informasi Monitoring</h3>
                    <div class="timeline-body">
                        Silahkan klik tombol Monitoring untuk menuju informasi Monitoring
                    </div>
                    <div class="timeline-footer">
                        <a href="{{ route('admin.dpl.detailMonitoring', ['dpl' => $dpl]) }}"
                            class="btn btn-primary btn-sm">Monitoring</a>
                    </div>
                </div>
            </div>
            <div>
                <i class="fas fa-clock bg-gray"></i>
            </div>
        </div>
    </div>
</div>
<!-- /.List -->
