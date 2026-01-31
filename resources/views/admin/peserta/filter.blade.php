@push('css')
    <style>
        /* Custom CSS */
        @media (max-width: 575.98px) {
            .mobile-width {
                width: 100% !important;
                /* Apply w-100 only on mobile */
            }
        }
    </style>
@endpush

{{-- FILTER --}}
<div class="row">
    <div class="col-12">
        <form action="{{ route('admin.peserta.export') }}" method="POST" target="_blank">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <select class="form-control select2bs4 w-100" id="tahun_id" name="tahun_id" required>
                            <option value="*">SEMUA TAHUN</option>
                            @foreach ($tahun as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <select class="form-control select2bs4 w-100" id="prodi_id" name="prodi_id" required>
                            <option value="*">SEMUA PROGRAM STUDI</option>
                            <option value="S1">SEMUA PRODI S1</option>
                            <option value="PASCA">SEMUA PRODI PASCA</option>
                            @foreach ($prodi as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <select class="form-control select2bs4 w-100" id="jenis_kelamin" name="jenis_kelamin" required>
                            @if (\Auth::user()->jenis_kelamin == '*')
                                <option value="*">SEMUA JENIS KELAMIN</option>
                                @foreach (BulkData::jenisKelamin as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            @else
                                <option value="{{ \Auth::user()->jenis_kelamin }}">
                                    {{ strtoupper(\Auth::user()->jenis_kelamin) }}</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <select class="form-control select2bs4 w-100" id="tanggal" name="tanggal" required>
                            <option value="*">SEMUA TANGGAL</option>
                            <option value="-">Pilih Tanggal Dibawah</option>
                        </select>
                    </div>
                    <div class="form-group d-none" id="form_group_range_tanggal">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control float-right" id="range_tanggal"
                                name="range_tanggal">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <select class="form-control select2bs4 w-100" id="status_id" name="status_id" required>
                            <option value="*">SEMUA STATUS</option>
                            @foreach ($status as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <select class="form-control select2bs4 w-100" id="jenis" name="jenis" required>
                            <option value="*">SEMUA JENIS</option>
                            @foreach (\Helper::getEnumValues('peserta', 'jenis') as $item)
                                <option value="{{ $item }}">{{ strtoupper($item) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end mb-3 flex-wrap align-content-center">
                <div class="ml-0 ml-sm-2 mt-3 mt-sm-0 mobile-width">
                    <a class="btn btn-success" href="" data-toggle="modal" data-target="#modal_import"
                        data-extension="xls,xlsx">Import Peserta</a>
                    <button class="btn btn-success mobile-width" type="submit" name="submit" value="excel">Export
                        Excel</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Import --}}
<form action="{{ route('admin.peserta.import') }}" method="POST" enctype="multipart/form-data" id="form_import">
    @csrf
    <div class="modal fade" id="modal_import" tabindex="-1" role="dialog" aria-labelledby="modal_import"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="title_edit">Import </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="upload-file">File</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="file" class="custom-file-input" id="upload-file"
                                    required>
                                <label class="custom-file-label" for="file" id="pilih_file">Pilih
                                    File</label>
                            </div>
                        </div>
                        <small><span class="text-danger">*</span>Ukuran file maksimal 20MB.
                            Tipe file
                            :
                            <span id="extension"></span>
                        </small>
                        <br>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="form_submit_import">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>



@push('script')
    <script>
        $('#modal_import').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            let extension = button.data('extension');

            extension = extension.split(',');
            extension = extension.map(str => {
                return `.${str}`;
            }).toString();

            var modal = $(this);
            $('#upload-file').attr('accept', extension);
            $('#extension').html(extension);
        })

        $('#form_import').submit(function(e) {
            e.preventDefault();

            var url = "{{ route('admin.peserta.import') }}";
            var fd = new FormData($('#form_import')[0]);

            $.ajax({
                type: "post",
                url: url,
                data: fd,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#form_submit_import').attr('disabled', true);
                },
                success: function(response) {
                    console.log(response);
                    $('#modal_import').modal('toggle');
                    $('#form_submit_import').prop("disabled", false);
                    swalToast(response.message, response.data);
                    if (typeof cardRefresh === 'function') {
                        cardRefresh();
                    }
                    if (document.getElementById('card_refresh_peserta')) {
                        document.getElementById('card_refresh_peserta').click();
                    }
                }
            });
        });
    </script>
@endpush
