{{-- Modal Tambah --}}
<form action="" id="form_add_pembayaran_s1" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="modal fade" id="modal_add_pembayaran_s1" tabindex="-1" role="dialog"
        aria-labelledby="modal_add_pembayaran_s1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Pembayaran S1</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('admin.pembayaran.form', ['jenjang' => 's1'])
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="form_submit_s1" disabled>Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>
@push('script')
    <script>
        var jenisKelamin = null;
        $(document).ready(function() {
            //autocomplete s1
            $("#search_s1").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        type: "get",
                        data: {
                            term: request.term
                        },
                        url: "{{ route('operasi.peserta.autocomplete') }}",
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                select: function(event, ui) {
                    var valItem = ui.item.value;

                    valItem = valItem.split('-');
                    valItem = valItem[0].substr(0, valItem[0].length - 1);
                    $('#search_s1').val(valItem);
                    document.getElementById('search_btn_s1').click();
                    return false; // make #search can edit
                },
                open: function(event, ui) {
                    $(this).autocomplete("widget").css({
                        "width": $(this).outerWidth()
                    });
                }
            });
        });

        $('#modal_add_pembayaran_s1').on('shown.bs.modal', function() {
            $('#search_s1').focus();
        })

        $('#form_add_pembayaran_s1').submit(function(e) {
            e.preventDefault();
            let fd = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{ route('admin.pembayaran.registrasi') }}",
                data: fd,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#form_submit_s1').attr('disabled', true);
                    $('#form_submit_s1').text('Loading...');
                },
                success: function(response) {
                    // console.log(response);
                    $('#modal_add_pembayaran_s1').modal('hide');
                    swalToast(response.message, response.data);
                    $('#search_s1').val('');
                    $('#search_btn_s1').click();
                    cardRefresh();
                },
                complete: function() {
                    $('#form_submit_s1').attr('disabled', false);
                    $('#form_submit_s1').text('Simpan');
                }
            });
        });

        // $('#tahun_s1').change(function(e) {
        //     setJenisKelaminS1();
        // });

        function setJenisKelaminS1() {
            $.ajax({
                type: "GET",
                url: "{{ route('operasi.kuota.getData') }}",
                data: {
                    tahun_id: $('#tahun_s1').val(),
                    jenjang: 'S1',
                },
                dataType: "json",
                beforeSend: function() {
                    $('#jenis_kelamin_s1').empty();
                    $('#jenis_kelamin_s1').append('<option value="">Loading...</option>');
                },
                success: function(response) {
                    // console.log(response);
                    if (!response.status) {
                        $('#jenis_kelamin_s1').empty();
                        $('#jenis_kelamin_s1').append('<option value="">Pilih Tahun Terlebih Dahulu</option>');
                    }

                    if (response.status) {
                        $('#jenis_kelamin_s1').empty();
                        $('#jenis_kelamin_s1').append('<option value="">Siap dipilih..</option>');
                        response.data.forEach(kuota => {
                            $('#jenis_kelamin_s1').append(`
                                <option value="${kuota.jenis}_${kuota.kuota}" ${kuota.jenis == jenisKelamin ? 'selected' : ''}>${kuota.jenis} (${kuota.kuota})</option>
                            `);
                        });
                    }
                }
            });
        }

        function cekPesertaS1(eModal, eSearch, eNim, eNama, eJenisKelamin, eProdi, eJenis, eTahun,
            eJenisPembayaran, eJumlah, eKamar, eUkuranBaju, eMursal, eKeterangan, eFormSubmit) {

            $(eNama).prop('disabled', true);
            $(eJenisKelamin).prop('disabled', true).trigger('change');
            $(eProdi).prop('disabled', true).trigger('change');
            $(eJenis).prop('disabled', true).trigger('change');
            $(eTahun).prop('disabled', true).trigger('change');
            $(eJenisPembayaran).prop('disabled', true).trigger('change');
            $(eJumlah).prop('disabled', true);
            $(eKamar).prop('disabled', true);
            $(eUkuranBaju).prop('disabled', true).trigger('change');
            $(eMursal).prop('disabled', true).trigger('change');
            $(eKeterangan).prop('disabled', true);
            $(eFormSubmit).prop('disabled', true);
            $.ajax({
                type: "POST",
                url: "{{ route('operasi.peserta.getData') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    search: $(eSearch).val()
                },
                beforeSend: function() {
                    $(`${eModal} .overlay`).remove();
                    var div = '<div class="overlay bkg-white">' +
                        '<i class="fas fa-2x fa-sync-alt fa-spin"></i>' +
                        '</div>';
                    $(`${eModal} .modal-header`).append(div);
                },
                success: function(response) {
                    let data = response.data;
                    jenisKelamin = data?.user?.jenis_kelamin ?? null;

                    if (response.status == false) {
                        $(eNim).val('');
                        $(eNama).val('');
                        $(eJenisKelamin).val('').change();
                        $(eJenis).val('').change();
                        $(eTahun).val('').change();
                        $(eProdi).val('').change();
                        $(eKamar).val('');
                        $(eUkuranBaju).val('').change();
                        $(eMursal).val('').change();
                        return;
                    }

                    // cek pasca atau sarjana
                    if (data.prodi.jenjang != "S1") {
                        swalToast(500, 'Bukan mahasiswa S1');
                        $(eNim).val('');
                        $(eNama).val('');
                        $(eJenisKelamin).val('').change();
                        $(eJenis).val('').change();
                        $(eTahun).val('').change();
                        $(eProdi).val('').change();
                        $(eKamar).val('');
                        $(eUkuranBaju).val('').change();
                        $(eMursal).val('').change();
                        return;
                    }

                    // cek valid kkn
                    if (data.kkn != true) {
                        swalToast(500, 'Belum Valid Untuk KRS');
                        $(eNim).val('');
                        $(eNama).val('');
                        $(eJenisKelamin).val('').change();
                        $(eJenis).val('').change();
                        $(eTahun).val('').change();
                        $(eProdi).val('').change();
                        $(eKamar).val('');
                        $(eUkuranBaju).val('').change();
                        $(eMursal).val('').change();
                        return;
                    }

                    $(eNama).removeAttr('disabled');
                    $(eJenis).prop('disabled', false).trigger('change');
                    $(eTahun).prop('disabled', false).trigger('change');
                    $(eJenisPembayaran).prop('disabled', false).trigger('change');
                    $(eKamar).removeAttr('disabled');
                    $(eUkuranBaju).prop('disabled', false).trigger('change');
                    $(eMursal).prop('disabled', false).trigger('change');
                    $(eJumlah).removeAttr('disabled');
                    $(eKeterangan).removeAttr('disabled');
                    $(eFormSubmit).removeAttr('disabled');

                    $(eNim).val(data.nim);
                    $(eNama).val(data.nama);
                    $(eProdi).val(data.prodi_id).change();
                    $(eProdi + "_hidden").val(data.prodi_id);
                    $(eJenisKelamin).val(data.jk.nama).change();
                    $(eJenisKelamin + "_hidden").val(data.jk.nama);
                    $(eJenis).val(data?.peserta?.jenis).change();
                    $(eTahun).val(data?.peserta?.tahun_id).change();
                    $(eKamar).val(data?.peserta?.kamar);
                    $(eUkuranBaju).val(data?.peserta?.ukuran_baju).change();
                    $(eMursal).val(data?.peserta?.mursal).change();
                },
                complete: function(response) {
                    $(`${eModal} .overlay`).remove();
                }
            });
        }

        function baruS1(eModal, eSearch, eNim, eNama, eJenisKelamin, eProdi,
            eTahun, eJenisPembayaran, eJumlah, eKamar, eUkuranBaju, eMursal, eKeterangan, eFormSubmit) {

            $(eNim).prop('disabled', true);
            $(eNama).prop('disabled', true);
            $(eJenisKelamin).prop('disabled', true).trigger('change');
            $(eProdi).prop('disabled', true).trigger('change');
            $(eTahun).prop('disabled', true).trigger('change');
            $(eJenisPembayaran).prop('disabled', true).trigger('change');
            $(eJumlah).prop('disabled', true);
            $(eKamar).prop('disabled', true);
            $(eUkuranBaju).prop('disabled', true).trigger('change');
            $(eMursal).prop('disabled', true).trigger('change');
            $(eKeterangan).prop('disabled', true);
            $(eFormSubmit).prop('disabled', true);

            $(eNim).val('');
            $(eNama).val('');
            $(eJenisKelamin).val('').change();
            $(eProdi).val('').change();
            $(eTahun).val('').change();
            $(eJenisPembayaran).val('').change();
            $(eJumlah).val('');
            $(eKamar).val('');
            $(eUkuranBaju).val('').change();
            $(eMursal).val('').change();
            $(eKeterangan).val('');

            $(eSearch).val('');
            swalToast(200, "REFRESH !! Data Baru");
        }
    </script>
@endpush
