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
<form action="{{ route('admin.dpl.export') }}" method="POST" target="_blank">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <select class="form-control select2bs4 w-100" id="f_prodi_id" name="prodi_id" required>
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
                        <select class="form-control select2bs4 w-100" id="f_jenis_kelamin" name="jenis_kelamin"
                            required>
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
        </div>
    </div>
    <div class="d-flex justify-content-end mb-3 flex-wrap align-content-center">
        <div class="ml-0 ml-sm-2 mt-3 mt-sm-0 mobile-width">
            <button class="btn btn-success mobile-width" type="submit" name="submit" value="excel">Export
                Excel</button>
        </div>
    </div>
</form>
@push('script')
    <script>
        $(document).ready(function() {
            $('#f_jenis_kelamin').change(function(e) {
                cardRefresh();
            });
            $('#f_prodi_id').change(function(e) {
                cardRefresh();
            });
        });
    </script>
@endpush
