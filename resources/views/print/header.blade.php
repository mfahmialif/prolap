<table class="table-print">
    <tr>
        <th class="text-center" width="15%" style="vertical-align:middle;">
            <img src="{{ asset('img/logo.png') }}" width="80" height="100" alt="">
        </th>

        <th class="text-left" width="85%" style="vertical-align:middle;color: #000;">
            <span style="font-size:22px;font-weight: bold">Universitas Islam Internasional Darullughah
                Wadda'wah</span><br>

            @if (@$prodi)
                <span style="font-size:18px;font-weight: bold">PROGRAM STUDI
                    {{ @strtoupper($prodi->nama) }} ({{ @strtoupper($prodi->jenjang) }})
                </span><br>
            @else
                <br>
            @endif

            <span style="font-size:12px;font-weight: bold">
                SK. Mendiknas RI Nomor 3530 Tahun 2013</span><br>
            <span class="alamat" style="font-size:10px;">
                Alamat : Jl. Raya Raci No. 51 PO BOX 8 Bangil Pasuruan Jawa Timur - KABUPATEN PASURUAN.
                Telp:0343-745317</span><br>
            <span class="email" style="font-size:10px;">
                Email : admin@uiidalwa.ac.id Website : http://www.uiidalwa.ac.id/</span>
        </th>
    </tr>
</table>
<hr>

<style>
    .x {
        background: #000;
        color: #fff;
        vertical-align: middle;
    }
</style>
