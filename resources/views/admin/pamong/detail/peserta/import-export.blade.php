{{-- DPL --}}
<div class="row">
    <div class="col-12">
        @if (\Auth::user()->role->nama == 'admin')
            <div class="alert alert-warning alert-dismissible">
                <h5><i class="icon fas fa-info"></i> Import Peserta!</h5>
                <a href="" data-toggle="modal" data-target="#modal_import" data-id="{{ $pamong->id }}"
                    data-extension="xls,xlsx">Klik disini untuk import peserta <i class="fas fa-hand-pointer ml-1"
                        aria-hidden="true"></i></a>
            </div>
        @endif
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
@if (\Auth::user()->role->nama == 'admin')
    @include('admin.pamong.import')
@endif
