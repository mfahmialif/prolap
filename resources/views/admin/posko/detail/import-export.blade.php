{{-- DPL --}}
<div class="row">
    <div class="col-12">
        @if (\Auth::user()->role->nama == 'dpl' || \Auth::user()->role->nama == 'admin')
            <div class="alert alert-info alert-dismissible">
                <h5><i class="icon fas fa-info"></i> Cetak Absensi DPL!</h5>
                <a href="{{ route('admin.posko.cetakAbsensi', ['posko' => $posko, 'tipe' => 'DPL']) }}"
                    target="_blank">Klik
                    disini untuk cetak absensi DPL <i class="fas fa-hand-pointer ml-1"></i></a>
            </div>
        @endif
        @if (\Auth::user()->role->nama == 'pengawas' || \Auth::user()->role->nama == 'admin')
            <div class="alert alert-success alert-dismissible">
                <h5><i class="icon fas fa-info"></i> Cetak Absensi Pengawas!</h5>
                <a href="{{ route('admin.posko.cetakAbsensi', ['posko' => $posko, 'tipe' => 'pengawas']) }}"
                    target="_blank">Klik disini untuk cetak absensi Pengawas <i class="fas fa-hand-pointer ml-1"
                        aria-hidden="true"></i></a>
            </div>
        @endif
        @if (\Auth::user()->role->nama == 'admin')
            <div class="alert alert-warning alert-dismissible">
                <h5><i class="icon fas fa-info"></i> Import Peserta!</h5>
                <a href="" data-toggle="modal" data-target="#modal_import" data-id="{{ $posko->id }}"
                    data-extension="xls,xlsx">Klik disini untuk import peserta <i class="fas fa-hand-pointer ml-1"
                        aria-hidden="true"></i></a>
            </div>
        @endif
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
@if (\Auth::user()->role->nama == 'admin')
    @include('admin.posko.import')
@endif
