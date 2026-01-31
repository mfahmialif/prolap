@php
    $role = \Auth::user()->role->nama;
@endphp
@extends('layouts.' . $role . '.template')
@section('title', 'Admin | Input Penilaian')
@push('css')
    <style>
        /* Chrome, Safari, Edge, Opera */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type="number"] {
            -moz-appearance: textfield;
        }

        .line-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .line {
            flex: 1;
            border: none;
            height: 1px;
            background: rgba(0, 0, 0, .1);
        }
    </style>
@endpush
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 d-flex flex-row">
                        <h1>Input Penilaian</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Penilaian
                            </li>
                            <li class="breadcrumb-item">DPL
                            </li>
                            <li class="breadcrumb-item active">Input
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <div class="content">
            <div class="container-fluid">
                @if ($role != 'peserta')
                    <div class="alert alert-success" role="alert">
                        Silahkan Input Penilaian
                    </div>
                @endif

                @include('admin.penilaian.peserta.input.info')

                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-circle-left mx-2"></i>Kembali</a>

                @foreach ($poskoPeserta as $poskoPesertaItem)
                    @php
                        $posko = $poskoPesertaItem->posko;
                        $poskoDpl = $posko->poskoDpl;
                        $poskoPengawas = $posko->poskoPengawas;
                    @endphp

                    <div class="line-container mb-3">
                        <hr class="line">
                        <a href="#karya" class="btn btn-warning"><i class="fas fa-home mr-2"></i>{{ $posko->nama }}</a>
                    </div>

                    @foreach ($poskoDpl as $poskoDplItem)
                        <form action="{{ route('admin.penilaian.peserta.input.store', ['peserta' => $peserta]) }}"
                            method="POST" onsubmit="storeNilai(event)">
                            @csrf
                            @include('admin.penilaian.peserta.input.nilai', [
                                'id' => $poskoDplItem->id,
                                'supervisor' => $poskoDplItem->dpl,
                                'tipe' => 'dpl',
                                'tipePeserta' => 'posko',
                                'jenis' => 'posko_dpl',
                                'jenisId' => $poskoDplItem->id,
                                'peserta' => $poskoPesertaItem,
                                'tahunId' => $poskoDplItem->posko->tahun_id,
                                'disabled' => $role == 'peserta' ? true : false,
                            ])
                            @if ($role != 'peserta')
                                <button type="submit" class="btn btn-success w-100">Simpan Nilai DPL</button>
                            @endif
                        </form>
                    @endforeach

                    <br>

                    @foreach ($poskoPengawas as $poskoPengawasItem)
                        <form action="{{ route('admin.penilaian.peserta.input.store', ['peserta' => $peserta]) }}"
                            method="POST" onsubmit="storeNilai(event)">
                            @csrf
                            @include('admin.penilaian.peserta.input.nilai', [
                                'id' => $poskoPengawasItem->id,
                                'supervisor' => $poskoPengawasItem->pengawas,
                                'tipe' => 'pengawas',
                                'tipePeserta' => 'posko',
                                'jenis' => 'posko_pengawas',
                                'jenisId' => $poskoPengawasItem->id,
                                'peserta' => $poskoPesertaItem,
                                'tahunId' => $poskoPengawasItem->posko->tahun_id,
                                'disabled' => $role == 'peserta' ? true : false,
                            ])
                        </form>
                    @endforeach
                @endforeach

                @foreach ($pamongPeserta as $pamongPesertaItem)
                    <div class="line-container mb-3">
                        <hr class="line">
                        <a href="#karya" class="btn btn-info"><i
                                class="fas fa-home mr-2"></i>{{ $pamongPesertaItem->pamong->nama }}</a>
                    </div>

                    <form action="{{ route('admin.penilaian.peserta.input.store', ['peserta' => $peserta]) }}"
                        method="POST" onsubmit="storeNilai(event)">
                        @csrf
                        @include('admin.penilaian.peserta.input.nilai', [
                            'id' => $pamongPesertaItem->id,
                            'supervisor' => $pamongPesertaItem->pamong,
                            'tipe' => 'pamong',
                            'tipePeserta' => 'pamong',
                            'jenis' => 'pamong',
                            'jenisId' => $pamongPesertaItem->pamong_id,
                            'peserta' => $pamongPesertaItem,
                            'tahunId' => $pamongPesertaItem->pamong->tahun_id,
                            'disabled' => $role == 'peserta' ? true : false,
                        ])
                        @if ($role != 'peserta')
                            <button type="submit" class="btn btn-success w-100">Simpan Nilai Pamong</button>
                        @endif
                    </form>
                @endforeach
            </div>
        </div>
    </div>
    </div>
@endsection
@push('script')
    <script>
        var komponenNilai = @json($komponenNilai);

        function setNilaiAkhir(jenis, event) {
            var nilaiElement = $(event.currentTarget).val();
            if (nilaiElement < 0) {
                $(event.currentTarget).val(0);
            }
            if (nilaiElement > 100) {
                $(event.currentTarget).val(100);
            }

            var nilaiAkhir = 0;
            komponenNilai.forEach(element => {
                if (jenis.split('_')[0] == element.jenis) {
                    var nilai = $('#nilai_' + element.nama + '_' + jenis).val();
                    var bobot = element.bobot;
                    nilaiAkhir += nilai * bobot / 100;
                }
            });

            $('#nilai_akhir_' + jenis).val(nilaiAkhir);
        }

        function storeNilai(event) {
            event.preventDefault();
            var form = $(event.currentTarget);
            var url = form.attr('action');
            var method = form.attr('method');
            var fd = new FormData($(form)[0]);
            var tipe = form.find('input[name="tipe"]').val();

            $.ajax({
                type: "post",
                url: url,
                data: fd,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $(form).find('button[type="submit"]').attr('disabled', true);
                },
                complete: function() {
                    $(form).find('button[type="submit"]').attr('disabled', false);
                },
                success: function(response) {
                    swalToast(response.message, response.data);
                    document.getElementById('card_refresh_' + tipe).click();
                    document.getElementById('card_refresh_info').click();
                }
            });
        }
    </script>
@endpush
