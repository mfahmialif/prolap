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
        <form action="{{ route('admin.absensi.data.dpl') }}" method="POST" target="_blank">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <select class="form-control select2bs4 w-100" id="tahun_id" name="tahun_id" required>
                            <option value="*">SEMUA TAHUN</option>
                            @foreach ($tahun as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
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

                <div class="col-md-6">
                    <div class="form-group">
                        <select class="form-control select2bs4 w-100 jk" id="jenis_kelamin" name="jenis_kelamin" required>
                            @if (\Auth::user()->jenis_kelamin == '*')
                                <option value="*">SEMUA JENIS KELAMIN</option>
                                @foreach (\Helper::getEnumValues('users', 'jenis_kelamin') as $item)
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
        </form>
    </div>
</div>
