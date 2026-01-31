<div class="table-responsive">
    <table id="{{ $table }}" class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                @foreach ($header as $item)
                    <th>{{ $item }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
@push('script')
    <script>
        $(document).ready(function() {
            var columns = [];

            @foreach ($columns as $item)
                if ('{{ $item['data'] }}' == 'id') {
                    columns.push({
                        data: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        className: "align-middle"
                    });
                } else {
                    columns.push({
                        data: '{{ $item['data'] }}',
                        name: '{{ $item['name'] }}',
                        searchable: {{ $item['searchable'] }},
                        orderable: {{ $item['orderable'] }},
                        className: "align-middle"
                    });
                }
            @endforeach

            {{ $table }} = $('#{{ $table }}').DataTable({
                // responsive: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                search: {
                    return: true,
                },
                "pagingType": "input",
                ajax: {
                    url: "{{ $url }}",
                    data: function(d) {
                        @foreach ($filter as $item)
                            d.{{ $item }} = $('#{{ $item }}').val();
                        @endforeach
                    },
                    beforeSend: function() {
                        $('.overlay').remove();
                        var div = '<div class="overlay">' +
                            '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                            '</div>';
                        $('#{{ $cardId }}').append(div);
                    },
                    complete: function() {
                        $('.overlay').remove();
                    }
                },
                deferRender: true,
                columns: columns
            })
            {{ $table }}.buttons().container().appendTo('#{{ $table }}' +
                '_wrapper .col-md-6:eq(0)');
        });
    </script>
@endpush
