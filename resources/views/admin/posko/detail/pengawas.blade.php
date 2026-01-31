{{-- Pengawas --}}
<div class="row">
    <div class="col-12">
        <div class="card" id="card_pengawas">
            <div class="card-body" id="card_body_pengawas">
                <p class="text-bold">Pengawas</p>
                <hr>
                <div class="form-group">
                    <div class="input-group">
                        <input type="input" name="search" class="form-control" id="search_pengawas"
                            placeholder="Masukkan username / nama pengawas" onfocus="this.select();">
                        <button type="button" class="btn btn-primary" id="search_btn_pengawas" style="cursor: pointer"
                            onclick="searchPengawas()">
                            <i class="fa fa-search"></i>
                        </button>
                        <button type="button" class="btn btn-warning ml-1" id="search_btn_pengawas_baru"
                            style="cursor: pointer" onclick="pengawasBaru()">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <form id="form_add_pengawas" action="{{ route('admin.posko.addPengawas', ['posko' => $posko]) }}"
                    method="POST">
                    @csrf
                    {{-- Hidden --}}
                    <input type="hidden" name="pengawas_id" id="pengawas_id">
                    {{-- End Hidden --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="username_pengawas">Username</label>
                                <input type="text" name="username" id="username_pengawas" class="form-control"
                                    placeholder="Masukkan Username Pengawas" disabled>
                            </div>
                        </div>
                    </div>
                    @include('components.admin.pengawas.form', ['param' => 'pengawas'])
                    <button type="submit" class="btn btn-success w-100">Simpan</button>
                </form>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
@push('script')
    <script>
        $(document).ready(function() {
            @if ($poskoPengawas)
                var pengawas = @json($poskoPengawas);
                $('#search_pengawas').val(pengawas['label']);
                $('#pengawas_id').val(pengawas['value']);
                document.getElementById('search_btn_pengawas').click();
            @endif

            $("#search_pengawas").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        type: "get",
                        data: {
                            term: request.term
                        },
                        url: "{{ route('operasi.pengawas.autocomplete') }}",
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                select: function(event, ui) {
                    var value = ui.item.value;
                    var label = ui.item.label;

                    $('#search_pengawas').val(label);
                    $('#pengawas_id').val(value);
                    document.getElementById('search_btn_pengawas').click();
                    return false; // make #search can edit
                },
                open: function(event, ui) {
                    $(this).autocomplete("widget").css({
                        "width": $(this).outerWidth()
                    });
                }
            });

            $('#form_add_pengawas').submit(function(e) {
                e.preventDefault();
                let fd = new FormData(this);
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.posko.addPengawas', ['posko' => $posko]) }}",
                    data: fd,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('#form_add_pengawas button[type="submit"]').attr('disabled', true);
                        $('#form_add_pengawas button[type="submit"]').text('Loading...');
                    },
                    success: function(response) {
                        if (response.status) {
                            var pengawas = response.pengawas;
                            $('#search_pengawas').val(
                                `${pengawas.user.username} - ${pengawas.nama}`);
                            $('#pengawas_id').val(`${pengawas.id}`);
                            document.getElementById('search_btn_pengawas').click();
                        }
                        swalToast(response.message, response.data);
                    },
                    complete: function() {
                        $('#form_add_pengawas button[type="submit"]').attr('disabled', false);
                        $('#form_add_pengawas button[type="submit"]').text('Simpan');
                    }
                });
            });
        });

        function searchPengawas() {
            $.ajax({
                method: "get",
                url: "{{ route('operasi.pengawas.getData') }}",
                data: {
                    id: $('#pengawas_id').val()
                },
                dataType: "json",
                beforeSend: function() {
                    $('#overlay_pengawas').remove();
                    var div = '<div class="overlay" id="overlay_pengawas">' +
                        '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                        '</div>';
                    $('#card_pengawas').append(div);
                },
                success: function(response) {
                    if (!response.status) {
                        swalToast(500, "Username / Nama Pengawas Tidak Ditemukan");
                    }

                    var data = response.data;

                    $('#username_pengawas').val(data?.user?.username);
                    $('#nama_pengawas').val(data?.nama);
                    $('#jenis_kelamin_pengawas').val(data?.user?.jenis_kelamin).change();
                    $('#hp_pengawas').val(data.hp);

                },
                complete: function() {
                    $('#overlay_pengawas').remove();
                }
            });
        }

        function pengawasBaru() {
            $('#search_pengawas').val("");
            $('#pengawas_id').val("");
            $('#username_pengawas').val("");
            $('#nama_pengawas').val("");
            $('#jenis_kelamin_pengawas').val("").change();
            $('#hp_pengawas').val("");
        }
    </script>
@endpush
