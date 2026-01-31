@php
    $param = isset($param) ? "_$param" : '';
    $cardId = $cardId ?? '';
    $posko = $posko ?? false;
@endphp
@if ($param == '')
    <div class="form-group">
        <div class="input-group">
            <input type="input" name="search" class="form-control" id="search_dpl{{ $param }}"
                placeholder="Masukkan NIY / Nama Dosen" onfocus="this.select();">
            <button type="button" class="btn btn-primary" id="search_btn_dpl{{ $param }}" style="cursor: pointer"
                onclick="searchDpl{{ $param }}()" />
            <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
    {{-- Hidden --}}
    <input type="hidden" name="dpl_id" id="dpl_id{{ $param }}">
    <input type="hidden" name="dosen_id" id="dosen_id{{ $param }}">
@endif
@if ($param == '_edit')
    <input type="hidden" name="id" id="id_edit">
@endif
{{-- End Hidden --}}
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="nama_dpl{{ $param }}">Nama</label>
            <input type="text" name="nama" class="form-control" id="nama_dpl{{ $param }}" required
                placeholder="Masukkan Nama Dosen">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="niy_dpl{{ $param }}">NIY (Username)</label>
            <input type="text" name="niy" class="form-control" id="niy_dpl{{ $param }}" readonly
                placeholder="Masukkan NIY Dosen">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="prodi_id_dpl{{ $param }}">Prodi</label>
            <select class="form-control select2bs4 w-100" name="prodi_id" id="prodi_id_dpl{{ $param }}"
                required>
                <option value="">Pilih Prodi Dosen</option>
                @foreach ($prodi as $item)
                    <option value="{{ $item->id }}">{{ strtoupper($item->nama) }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="jenis_kelamin_dpl{{ $param }}">Jenis Kelamin</label>
            <select class="form-control select2bs4 w-100" name="jenis_kelamin"
                id="jenis_kelamin_dpl{{ $param }}" required>
                <option value="">Pilih Jenis Kelamin</option>
                @foreach (\Helper::getEnumValues('users', 'jenis_kelamin', ['*']) as $item)
                    <option value="{{ $item }}">{{ strtoupper($item) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="email_dpl{{ $param }}">Email</label>
            <input type="email" name="email" class="form-control" id="email_dpl{{ $param }}"
                placeholder="Masukkan Email Dosen">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="hp_dpl{{ $param }}">Nomer HP</label>
            <input type="text" name="hp" class="form-control" id="hp_dpl{{ $param }}"
                placeholder="Masukkan Nomer HP Dosen">
        </div>
    </div>
</div>
@push('script')
    <script>
        $(document).ready(function() {
            //autocomplete s1
            $("#search_dpl{{ $param }}").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        type: "get",
                        data: {
                            term: request.term
                        },
                        url: "{{ route('operasi.dosen.autocomplete') }}",
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                select: function(event, ui) {
                    var valItem = ui.item.value;

                    var data = valItem.split('-');
                    var dosenId = data[0];

                    $('#search_dpl{{ $param }}').val(valItem);
                    $('#dosen_id{{ $param }}').val(dosenId);
                    document.getElementById('search_btn_dpl{{ $param }}').click();
                    return false; // make #search can edit
                },
                open: function(event, ui) {
                    $(this).autocomplete("widget").css({
                        "width": $(this).outerWidth()
                    });
                }
            });
        });

        function searchDpl{{ $param }}() {
            $.ajax({
                method: "get",
                url: "{{ route('operasi.dosen.getData') }}",
                data: {
                    dosen_id: $('#dosen_id{{ $param }}').val()
                },
                dataType: "json",
                beforeSend: function() {
                    $('#overlay_dpl').remove();
                    var div = '<div class="overlay bg-trans-white" id="overlay_dpl">' +
                        '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                        '</div>';
                    @if ($cardId)
                        $('#{{ $cardId }}').append(div);
                    @else
                        $('#modal_add .modal-body').append(div);
                    @endif

                    $('#form_add button[type="submit"]').attr('disabled', true);
                    $('#form_add button[type="submit"]').text('Loading...');
                },
                success: function(response) {
                    if (!response.status) {
                        swalToast(500, "NIY / Nama Dosen Tidak Ditemukan");
                    }

                    var data = response.data;

                    $('#nama_dpl{{ $param }}').val(data.nama);
                    $('#niy_dpl{{ $param }}').val(data.kode);
                    $('#jenis_kelamin_dpl{{ $param }}').val(data.jenis_kelamin).change();
                    $('#prodi_id_dpl{{ $param }}').val(data.prodi_id).change();
                    $('#hp_dpl{{ $param }}').val(data.hp);
                    $('#email_dpl{{ $param }}').val(data.email);
                    $('#dpl_id{{ $param }}').val(null);

                    @if ($posko)
                        if (data.dpl) {
                            $('#jenis_kelamin_dpl{{ $param }}').val(data.dpl.user.jenis_kelamin)
                                .change();
                            $('#prodi_id_dpl{{ $param }}').val(data.dpl.prodi_id).change();
                            $('#hp_dpl{{ $param }}').val(data.dpl.hp);
                            $('#email_dpl{{ $param }}').val(data.dpl.user.email);
                            $('#nama_dpl{{ $param }}').val(data.dpl.nama);
                            $('#dpl_id{{ $param }}').val(data.dpl.id);
                        }
                    @endif
                },
                complete: function() {
                    $('#overlay_dpl').remove();
                    $('#form_add button[type="submit"]').attr('disabled', false);
                    $('#form_add button[type="submit"]').text('Simpan');
                }
            });
        }
    </script>
@endpush
