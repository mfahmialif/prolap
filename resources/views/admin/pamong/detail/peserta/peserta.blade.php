@push('css')
    <style>
        #item-list li.hidden {
            display: none !important;
        }
    </style>
@endpush
{{-- Peserta --}}
<div class="row">
    <div class="col-12">
        <div class="card" id="card_peserta">
            <div class="card-header p-2">
                <div class="card-tools m-1">
                    <button type="button" class="btn btn-tool" id="card_refresh_peserta" data-card-widget="card-refresh"
                        data-source="{{ url()->current() }}" data-source-selector="#card_body_peserta"
                        data-load-on-init="false">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" id="card_body_peserta">
                <p class="text-bold">Peserta</p>
                <hr>
                @if (\Auth::user()->role->nama == 'admin')
                    <div class="form-group">
                        <div class="input-group">
                            <input type="input" name="search" class="form-control" id="search_peserta"
                                placeholder="Masukkan NIM / Nama peserta" onfocus="this.select();">
                            <button type="button" class="btn btn-primary" id="search_btn_peserta"
                                style="cursor: pointer" onclick="inputPeserta()" />
                            <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    {{-- Hidden --}}
                    <input type="hidden" name="peserta_id" id="peserta_id">
                    {{-- End Hidden --}}
                    <hr>
                @endif
                <div class="form-group">
                    <input type="input" name="search" class="form-control" id="search_list_peserta"
                        placeholder="Untuk mencari peserta PPL/PKL yang ada di list di bawah ini" onfocus="this.select();"
                        autocomplete="off">
                </div>
                <ul class="list-group list-group-flush">
                    @if (\Auth::user()->role->nama == 'admin')
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="w-75">
                                <input type="checkbox" class="form-check-input" id="check_all"
                                    onchange="checkAll(event)">
                                <label class="form-check-label" for="check_all">Centang Semua</label>
                            </div>
                            <div><button class="btn btn-danger" onclick="deletePesertaChecked()"><i class="fa fa-trash"
                                        aria-hidden="true"></i></button>
                            </div>
                        </li>
                    @endif
                </ul>
                @if (\Auth::user()->role->nama == 'admin')
                    <hr>
                    <ul class="list-group list-group-flush" id="item-list">
                        @foreach ($pamong->pamongPeserta->sortByDesc('id') as $pamongPeserta)
                            @php
                                $nama =
                                    @$pamongPeserta->peserta->nim .
                                    ' - ' .
                                    @$pamongPeserta->peserta->nama .
                                    ' - ' .
                                    @$pamongPeserta->peserta->prodi->alias .
                                    ' - ' .
                                    @$pamongPeserta->peserta->user->jenis_kelamin;
                            @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="w-75">
                                    <input type="checkbox" class="form-check-input check_peserta_id"
                                        id="peserta_id_{{ $pamongPeserta->id }}" value="{{ $pamongPeserta->id }}">
                                    <label class="form-check-label"
                                        for="peserta_id_{{ $pamongPeserta->id }}">{{ $nama }}</label>
                                </div>
                                <div><button class="btn btn-danger"
                                        onclick="deletePeserta({{ $pamongPeserta->id }}, '{{ $nama }}')"><i
                                            class="fa fa-trash" aria-hidden="true"></i></button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <ul class="list-group list-group-flush" id="item-list">
                        @foreach ($pamong->pamongPeserta->sortByDesc('id') as $pamongPeserta)
                            @php
                                $nama =
                                    @$pamongPeserta->peserta->nim .
                                    ' - ' .
                                    @$pamongPeserta->peserta->nama .
                                    ' - ' .
                                    @$pamongPeserta->peserta->prodi->alias .
                                    ' - ' .
                                    @$pamongPeserta->peserta->user->jenis_kelamin;
                            @endphp
                            <li class="list-group-item">
                                <label class="form-check-label"
                                    for="peserta_id_{{ $pamongPeserta->id }}">{{ $nama }}</label>
                            </li>
                        @endforeach
                    </ul>
                @endif
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
        function searchListPeserta() {
            document.getElementById('search_list_peserta').addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                const listItems = document.querySelectorAll('#item-list li');

                listItems.forEach(function(item) {
                    const text = item.textContent.toLowerCase().trim();
                    if (text.includes(filter)) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
            });
        }
    </script>
@endpush

@if (\Auth::user()->role->nama == 'admin')
    @push('script')
        <script>
            $(document).ready(function() {
                initPeserta();

                $('#card_refresh_peserta').on('overlay.removed.lte.cardrefresh', function() {
                    initPeserta();
                    document.getElementById('search_peserta').focus();
                });
            });

            function initPeserta() {
                searchListPeserta();

                $("#search_peserta").autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            type: "get",
                            data: {
                                term: request.term
                            },
                            url: "{{ route('operasi.pesertaKkn.autocomplete') }}",
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        var value = ui.item.value;
                        var label = ui.item.label;

                        $('#search_peserta').val(label);
                        $('#peserta_id').val(value);
                        document.getElementById('search_btn_peserta').click();
                        return false; // make #search can edit
                    },
                    open: function(event, ui) {
                        $(this).autocomplete("widget").css({
                            "width": $(this).outerWidth()
                        });
                    }
                });
            }

            function inputPeserta() {
                var pesertaId = $('#peserta_id').val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.pamong.addPeserta', ['pamong' => $pamong]) }}",
                    data: {
                        'peserta_id': pesertaId,
                        '_token': "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $('#overlay_peserta').remove();
                        var div = '<div class="overlay" id="overlay_peserta">' +
                            '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                            '</div>';
                        $('#card_peserta').append(div);
                    },
                    complete: function() {
                        $('#overlay_peserta').remove();
                    },
                    success: function(response) {
                        swalToast(response.message, response.data);
                        document.getElementById('card_refresh_peserta').click();
                    },
                });
            }

            function checkAll(event) {
                $('.check_peserta_id').each(function() {
                    if (!$(this).closest('li').hasClass('hidden')) {
                        $(this).prop('checked', $(event.currentTarget).prop('checked'));
                    }
                });
                // $('.check_peserta_id').prop('checked', $(event.currentTarget).prop('checked'));
            }

            function deletePeserta(pamongPesertaId, nama) {
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
                        ajaxDeletePeserta(pamongPesertaId);
                    }
                })
            }

            function ajaxDeletePeserta(pamongPesertaId) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.pamong.deletePeserta', ['pamong' => $pamong]) }}",
                    data: {
                        'pamong_peserta_id': pamongPesertaId,
                        '_token': "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $('#overlay_peserta_' + pamongPesertaId).remove();
                        var div = '<div class="overlay" id="overlay_peserta_' + pamongPesertaId + '">' +
                            '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                            '</div>';
                        $('#card_peserta').append(div);
                    },
                    complete: function() {
                        $('#overlay_peserta_' + pamongPesertaId).remove();
                    },
                    success: function(response) {
                        toaster(response.message, response.data);
                        document.getElementById('card_refresh_peserta').click();
                    }
                });
            }

            function deletePesertaChecked() {
                Swal.fire({
                    title: `Yakin Ingin menghapus semua peserta PPL/PKL yang tercentang?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Iya',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        var checked = $('.check_peserta_id:checked');
                        if (checked.length > 0) {
                            var pesertaId = checked.map(function() {
                                return $(this).val();
                            }).get();

                            pesertaId.forEach(element => {
                                ajaxDeletePeserta(element);
                            });
                        }
                    }
                })

            }
        </script>
    @endpush
@else
    @push('script')
        <script>
            $(document).ready(function() {
                initPeserta();

                $('#card_refresh_peserta').on('overlay.removed.lte.cardrefresh', function() {
                    initPeserta();
                });
            });

            function initPeserta() {
                searchListPeserta();
            }
        </script>
    @endpush
@endif
