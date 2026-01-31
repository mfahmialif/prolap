<link rel="stylesheet" href="{{ public_path('css/cetak.css') }}">

@include('print.header')

<div style="font-size:12px;">
    <h3 class="text-center" style="font-size:14px;margin:3px;">ABSENSI MAHASISWA PPL/PKL</h3>
    <h3 class="text-center" style="font-size:14px;margin:3px;">TAHUN AKADEMIK {{ @$tahun->nama }}</h3><br>

    <table class="table-print">
        <thead>
            <tr>
                <td width="3%">Posko</td>
                <td width="1%">:</td>
                <td width="46%">{{ @$posko->nama }}</td>
                <td width="3%">Lokasi</td>
                <td width="1%">:</td>
                <td width="46%">{{ @$posko->lokasi }}</td>
            </tr>
            <tr>
                <td width="3%">DPL</td>
                <td width="1%">:</td>
                <td width="46%">
                    <ul class="header-cetak">
                        @foreach ($poskoDpl as $item)
                            <li>{{ @$item->dpl->nama }}</li>
                        @endforeach
                    </ul>
                </td>
                <td width="3%">Pengawas</td>
                <td width="1%">:</td>
                <td width="46%">
                    <ul class="header-cetak">
                        @foreach ($poskoPengawas as $item)
                            <li>{{ @$item->pengawas->nama }}</li>
                        @endforeach
                    </ul>
                </td>
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
                    <th class="text-center" width="5%">{{ $i+1 }}</th>
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

            @foreach ($poskoPeserta as $row)
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
        @for ($i = 2; $i >= 0; $i--)
            <td width="33%" class="text-center">
                @if ($i == 0)
                    <b>{{ $kota }}, {{ \Helper::formatDate(\Carbon::now()->format('Y-m-d')) }}</b><br />
                @endif
            </td>
        @endfor
    </tr>
    <tr>
        @for ($i = 2; $i >= 0; $i--)
            <td width="33%" class="text-center">
                @if (@$petugas['data'][$i])
                    {{ $petugas['jabatan'] }}, <br /><br /><br /><br />
                    <b><u>{{ @$petugas['data'][$i][$petugas['db']]['nama'] }}</u></b><br />
                @endif
            </td>
        @endfor
    </tr>
</table>

@include('print.footer')
