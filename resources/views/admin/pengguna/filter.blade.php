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
            <div class="col-md-6">
                <div class="form-group">
                    <select class="form-control select2bs4 w-100" id="f_role_id" name="role_id" required>
                        <option value="*">SEMUA ROLE</option>
                        @foreach ($role as $item)
                            <option value="{{ $item->id }}">{{ strtoupper($item->nama) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <select class="form-control select2bs4 w-100" id="f_jenis_kelamin" name="jenis_kelamin" required>
                        @if (\Auth::user()->jenis_kelamin == '*')
                            <option value="*">SEMUA JENIS KELAMIN</option>
                            @foreach (\Helper::getEnumValues('users', 'jenis_kelamin', ['*']) as $item)
                                <option value="{{ $item }}">{{ strtoupper($item) }}</option>
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
@push('script')
    <script>
        $(document).ready(function() {
            $('#f_role_id').change(function(e) {
                cardRefresh();
            });
            $('#f_jenis_kelamin').change(function(e) {
                cardRefresh();
            });
        });
    </script>
@endpush
