<h3 class="text-center my-3">Tugas</h3>
<table id="pamong-absensi" class="table table-striped datatable" style="width:100%">
    <thead>
        <tr>
            <th style="width: 40%" class="align-top text-start">Pamong</th>
            <th style="width: 40%" class="align-top">Nama</th>
            <th style="width: 20%" class="align-top">Jumlah Penugasan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($penugasanPamong as $item)
            <tr>
                @php
                    $dataOrder = explode(' ', trim($item->pamong_pamong));
                    $dataOrder = intval(end($dataOrder));
                @endphp
                <td data-order="{{ $dataOrder }}" class="text-start">{{ $item->pamong_pamong }}</td>
                <td>{{ $item->pamong_nama }}</td>
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
