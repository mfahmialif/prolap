{{-- DPL --}}
<div class="row">
    <div class="col-12">
        <div class="card" id="card_dpl">
            <div class="card-body">
                <form action="" id="form_add_dpl" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="card-body">
                        <p class="text-bold">DPL</p>
                        <hr>
                        @include('components.admin.dpl.form', ['cardId' => 'card_dpl', 'posko' => true])
                        <button type="submit" class="btn btn-success w-100" id="form_submit_dpl">Simpan</button>
                    </div>
                </form>


            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <div class="card" id="card_list_dpl">
            <div class="card-header p-2">
                <div class="card-tools m-1">
                    <button type="button" class="btn btn-tool" id="card_refresh_dpl" data-card-widget="card-refresh"
                        data-source="{{ url()->current() }}" data-source-selector="#card_body_dpl"
                        data-load-on-init="false">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" id="card_body_dpl">
                <ul class="list-group list-group-flush">
                    @if (\Auth::user()->role->nama == 'admin')
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="w-75">
                            <input type="checkbox" class="form-check-input" id="check_all_dpl"
                                onchange="checkAllDpl(event)">
                            <label class="form-check-label" for="check_all_dpl">Centang Semua</label>
                        </div>
                        <div><button class="btn btn-danger" onclick="deleteDplChecked()"><i class="fa fa-trash"
                                    aria-hidden="true"></i></button>
                        </div>
                    </li>
                    @endif
                </ul>
                @if (\Auth::user()->role->nama == 'admin')
                <hr>
                <ul class="list-group list-group-flush" id="item-list">
                    @foreach ($posko->poskoDpl->sortByDesc('id') as $item)
                    @php
                    $nama =
                    @$item->dpl->nama .
                    ' - ' .
                    @$item->dpl->prodi->alias .
                    ' - ' .
                    @$item->dpl->user->jenis_kelamin;
                    @endphp
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="w-75">
                            <input type="checkbox" class="form-check-input check_dpl_id"
                                id="dpl_id_{{ $item->id }}" value="{{ $item->id }}">
                            <label class="form-check-label"
                                for="dpl_id_{{ $item->id }}">{{ $nama }}</label>
                        </div>
                        <div><button class="btn btn-danger"
                                onclick="deleteDpl({{ $item->id }}, '{{ $nama }}')"><i
                                    class="fa fa-trash" aria-hidden="true"></i></button>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <ul class="list-group list-group-flush" id="item-list">
                    @foreach ($posko->poskoDpl->sortByDesc('id') as $item)
                    @php
                    $nama =
                    @$item->dpl->nama .
                    ' - ' .
                    @$item->dpl->prodi->alias .
                    ' - ' .
                    @$item->dpl->user->jenis_kelamin;
                    @endphp
                    <li class="list-group-item">
                        <label class="form-check-label"
                            for="dpl_id_{{ $item->id }}">{{ $nama }}</label>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

@push('script')
<script>
    $(document).ready(function() {
        @if($poskoDpl)
        // var dpl = "{{ $poskoDpl }}";
        // $('#search_dpl').val(dpl);
        // var data = dpl.split('-');
        // var dosenId = data[0];
        // $('#dosen_id').val(dosenId);
        // document.getElementById('search_btn_dpl').click();
        @endif

        $('#form_add_dpl').submit(function(e) {
            e.preventDefault();
            let fd = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{ route('admin.posko.addDpl', ['posko' => $posko]) }}",
                data: fd,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#form_submit_dpl').attr('disabled', true);
                    $('#form_submit_dpl').text('Loading...');
                },
                success: function(response) {
                    console.log(response);

                    swalToast(response.message, response.data);
                },
                complete: function() {
                    $('#form_submit_dpl').attr('disabled', false);
                    $('#form_submit_dpl').text('Simpan');
                    document.getElementById('card_refresh_dpl').click();
                }
            });
        });
    });

    function deleteDplChecked() {
        Swal.fire({
            title: `Yakin Ingin menghapus semua DPL PPL/PKL yang tercentang?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Iya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var checked = $('.check_dpl_id:checked');
                if (checked.length > 0) {
                    var dplId = checked.map(function() {
                        return $(this).val();
                    }).get();

                    dplId.forEach(element => {
                        ajaxDeleteDpl(element);
                    });
                }
            }
        })
    }


    function ajaxDeleteDpl(poskoDplId) {
        $.ajax({
            type: "POST",
            url: "{{ route('admin.posko.deleteDpl', ['posko' => $posko]) }}",
            data: {
                'posko_dpl_id': poskoDplId,
                '_token': "{{ csrf_token() }}"
            },
            dataType: "json",
            beforeSend: function() {
                $('#overlay_dpl_' + poskoDplId).remove();
                var div = '<div class="overlay" id="overlay_dpl_' + poskoDplId + '">' +
                    '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                    '</div>';
                $('#card_dpl').append(div);
            },
            complete: function() {
                $('#overlay_dpl_' + poskoDplId).remove();
            },
            success: function(response) {
                toaster(response.message, response.data);
                document.getElementById('card_refresh_dpl').click();
            }
        });
    }

    function checkAllDpl(event) {
        $('.check_dpl_id').each(function() {
            if (!$(this).closest('li').hasClass('hidden')) {
                $(this).prop('checked', $(event.currentTarget).prop('checked'));
            }
        });
        // $('.check_dpl_id').prop('checked', $(event.currentTarget).prop('checked'));
    }

    function deleteDpl(poskoDplId, nama) {
        Swal.fire({
            title: `Yakin Ingin menghapus ${nama}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Iya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                ajaxDeleteDpl(poskoDplId);
            }
        })
    }
</script>
@endpush