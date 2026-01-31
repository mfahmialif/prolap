<div class="card card-primary card-outline">
    <div class="card-body">
        <div class="tab-content">
            <h4>Pamong</h4>
            <div class="post">
                <table>
                    @foreach ($peserta->pamongPeserta as $pamongPeserta)
                        <tr>
                            <td width="200">Nama Pamong</td>
                            <td>: {{ $pamongPeserta->pamong->nama }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
