<div class="card card-primary card-outline">
    <div class="card-body">
        <div class="tab-content">
            <h4>Data Diri</h4>
            <div class="post">
                <table>
                    <tr>
                        <td width="200">JENIS</td>
                        <td>: {{ strtoupper(@$peserta->jenis) }}</td>
                    </tr>
                    <tr>
                        <td width="200">NAMA LENGKAP</td>
                        <td>: {{ strtoupper(@$peserta->nama) }}</td>
                    </tr>
                    <tr>
                        <td width="200">NAMA PONDOK</td>
                        <td>: {{ strtoupper(@$peserta->nama_pondok) }}</td>
                    </tr>
                    <tr>
                        <td width="200">NIM</td>
                        <td>: {{ strtoupper(@$peserta->nim) }}</td>
                    </tr>
                    <tr>
                        <td width="200">NIK</td>
                        <td>: {{ strtoupper(@$peserta->nik) }}</td>
                    </tr>
                    <tr>
                        <td width="200">PRODI</td>
                        <td>: {{ strtoupper(@$peserta->prodi->nama) }}</td>
                    </tr>
                    <tr>
                        <td width="200">JENIS KELAMIN</td>
                        <td>: {{ strtoupper(@$peserta->user->jenis_kelamin) }}</td>
                    </tr>
                    <tr>
                        <td width="200">TEMPAT LAHIR</td>
                        <td>: {{ strtoupper(@$peserta->tempat_lahir) }}</td>
                    </tr>
                    <tr>
                        <td width="200">TANGGAL LAHIR</td>
                        <td>: {{ date('d M Y', strtotime(@$peserta->tanggal_lahir)) }}</td>
                    </tr>
                    <tr>
                        <td width="200">NO TELEPON/WHATSAPP</td>
                        <td>: {{ @$peserta->nomor_hp }}</td>
                    </tr>
                    <tr>
                        <td width="200">NO TELEPON/WHATSAPP ORANG TUA</td>
                        <td>: {{ @$peserta->nomor_hp_orang_tua }}</td>
                    </tr>
                    <tr>
                        <td width="200">ALAMAT</td>
                        <td>: {{ strtoupper(@$peserta->alamat) }}</td>
                    </tr>
                    <tr>
                        <td width="200">UKURAN BAJU</td>
                        <td>: {{ strtoupper(@$peserta->ukuran_baju) }}</td>
                    </tr>
                    <tr>
                        <td width="200">KAMAR</td>
                        <td>: {{ strtoupper(@$peserta->kamar) }}</td>
                    </tr>
                    <tr>
                        <td width="200">KELAS PONDOK</td>
                        <td>: {{ strtoupper(@$peserta->kelas_pondok) }}</td>
                    </tr>
                    <tr>
                        <td width="200">QISM PONDOK</td>
                        <td>: {{ strtoupper(@$peserta->qism_pondok) }}</td>
                    </tr>
                    <tr>
                        <td width="200">KEAHLIAN</td>
                        <td>: {{ strtoupper(@$peserta->keahlian) }}</td>
                    </tr>
                    <tr>
                        <td width="200">MAHIR BAHASA LOKAL</td>
                        <td>: {{ strtoupper(@$peserta->mahir_bahasa_lokal) }}</td>
                    </tr>
                    <tr>
                        <td width="200">MURSAL</td>
                        <td>: {{ strtoupper(@$peserta->mursal) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>