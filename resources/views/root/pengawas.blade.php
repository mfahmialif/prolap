<h3 class="text-center my-3">Absensi</h3>
<table id="pengawas-absensi" class="table table-hover datatable" style="width:100%">
    <thead>
        <tr>
            <th style="width: 40%" class="align-top text-start">Posko</th>
            <th style="width: 40%" class="align-top">Nama</th>
            <th style="width: 20%" class="align-top">Absensi Hari Ini</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($absensiPengawas as $item)
            <tr>
                @php
                    $dataOrder = explode(' ', trim($item->posko_nama));
                    $dataOrder = intval(end($dataOrder));
                @endphp
                <td data-order="{{ $dataOrder }}" class="text-start">{{ $item->posko_nama }}</td>
                <td>{{ $item->pengawas_nama }}</td>
                @if ($item->status == 'Sudah')
                    <td><span class="badge rounded-pill bg-success">{{ $item->status }}</span></td>
                @else
                    <td><span class="badge rounded-pill bg-danger">{{ $item->status }}</span></td>
                @endif
            </tr>
        @endforeach

    </tbody>
</table>
