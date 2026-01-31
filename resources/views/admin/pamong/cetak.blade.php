<link rel="stylesheet" href="{{ public_path('css/cetak.css') }}">

@include('print.header')

<div style="font-size:12px;">
    <h3 class="text-center" style="font-size:14px;margin:3px;">ABSENSI MAHASISWA PPL/PKL</h3>
    <h3 class="text-center" style="font-size:14px;margin:3px;">TAHUN AKADEMIK {{ @$tahun->nama }}</h3><br>

    <table class="table-print">
        <thead>
            <tr>
                <td width="3%">Nama</td>
                <td width="1%">:</td>
                <td width="46%">{{ @$pamong->nama }}</td>
                <td width="3%">Pamong</td>
                <td width="1%">:</td>
                <td width="46%">{{ @$pamong->pamong }}</td>
            </tr>
        </thead>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th class="text-center" width="10" rowspan="3">No</th>
                <th class="text-center" width="10%" rowspan="3">NIM</th>
                <th class="text-center" rowspan="3">NAMA MAHASISWA</th>
                <th class="text-center" width="5%" rowspan="3">L/P</th>
                <th class="text-center" colspan="{{ $jumlahPertemuan + 1 }}">PERTEMUAN KE / TANGGAL</th>
            </tr>

            <tr>
                @for ($i = 0; $i < $jumlahPertemuan; $i++)
                    <th class="text-center" width="5%">{{ $i + 1 }}</th>
                @endfor
                <th class="text-center" width="5%">KET</th>
            </tr>

            <tr>
                @for ($i = 0; $i < $jumlahPertemuan; $i++)
                    <th class="text-center" width="5%">/</th>
                @endfor
                <th class="text-center"></th>
            </tr>
        </thead>

        <tbody>
            @php
                $no = 1;
            @endphp

            @foreach ($pamongPeserta as $row)
                @php
                    $peserta = $row->peserta;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ @$peserta->nim }}</td>
                    <td>{{ @$peserta->nama }}</td>
                    <td class="text-center">{{ @substr($peserta->user->jenis_kelamin, 0, 1) }}</td>

                    @for ($i = 0; $i < $jumlahPertemuan; $i++)
                        <td class="text-center" width="5%"></td>
                    @endfor
                    <td class="text-center"></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<br />

<table>
    @php
        $kota = 'Raci';
    @endphp
    <tr>
        <td width="33%" class="text-center">
        </td>
        <td width="33%" class="text-center">
        </td>
        <td width="33%" class="text-center">
            <b>{{ $kota }}, {{ \Helper::formatDate(\Carbon::now()->format('Y-m-d')) }}</b><br />
        </td>
    </tr>
    <tr>
        <td width="33%" class="text-center">
        </td>
        <td width="33%" class="text-center">
        </td>
        <td width="33%" class="text-center">
            Pamong, <br /><br /><br /><br />
            <b><u>{{ @$pamong['nama'] }}</u></b><br />
        </td>
    </tr>
</table>

@include('print.footer')
