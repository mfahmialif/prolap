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
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <select class="form-control select2bs4 w-100" id="f_tahun_id" name="tahun_id" required>
                        <option value="*">SEMUA TAHUN</option>
                        @foreach ($tahun as $item)
                            <option value="{{ $item->id }}">{{ $item->kode }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script>
        $(document).ready(function() {
            $('#f_tahun_id').change(function(e) {
                cardRefresh();
            });
        });
    </script>
@endpush
