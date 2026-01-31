<h3 class="text-center my-3">Tugas</h3>
<table id="dpl-absensi" class="table table-hover datatable" style="width:100%">
    <thead>
        <tr>
            <th style="width: 40%" class="align-top text-start">Posko</th>
            <th style="width: 40%" class="align-top">Nama DPL</th>
            <th style="width: 20%" class="align-top">Jumlah Absensi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($absensiDpl as $item)
            <tr>
                @php
                    $dataOrder = explode(' ', trim($item->posko_nama));
                    $dataOrder = intval(end($dataOrder));
                @endphp
                <td data-order="{{ $dataOrder }}" class="text-start">{{ $item->posko_nama }}</td>
                <td>{{ $item->dpl_nama }}</td>
                @if ($item->jumlah_absensi >= 2)
                    <td><span class="badge rounded-pill bg-success">{{ $item->jumlah_absensi }}</span></td>
                @elseif ($item->jumlah_absensi >= 1 && $item->jumlah_absensi < 2)
                    <td><span class="badge rounded-pill bg-warning">{{ $item->jumlah_absensi }}</span></td>
                @else
                    <td><span class="badge rounded-pill bg-danger">{{ $item->jumlah_absensi }}</span></td>
                @endif
            </tr>
        @endforeach

    </tbody>
</table>
<hr>
<h3 class="text-center my-3">Penugasan</h3>
<table id="dpl-penugasan" class="table table-hover datatable" style="width:100%">
    <thead>
        <tr>
            <th style="width: 40%" class="align-top text-start">Posko</th>
            <th style="width: 40%" class="align-top">Nama DPL</th>
            <th style="width: 20%" class="align-top">Jumlah Penugasan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($penugasanDpl as $item)
            <tr>
                @php
                    $dataOrder = explode(' ', trim($item->posko_nama));
                    $dataOrder = intval(end($dataOrder));
                @endphp
                <td data-order="{{ $dataOrder }}" class="text-start">{{ $item->posko_nama }}</td>
                <td>{{ $item->dpl_nama }}</td>
                @if ($item->jumlah_tugas >= 2)
                    <td><span class="badge rounded-pill bg-success">{{ $item->jumlah_tugas }}</span></td>
                @elseif ($item->jumlah_tugas >= 1 && $item->jumlah_tugas < 2)
                    <td><span class="badge rounded-pill bg-warning">{{ $item->jumlah_tugas }}</span></td>
                @else
                    <td><span class="badge rounded-pill bg-danger">{{ $item->jumlah_tugas }}</span></td>
                @endif
            </tr>
        @endforeach

    </tbody>
</table>
