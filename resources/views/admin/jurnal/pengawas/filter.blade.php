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
        <form action="{{ route('admin.penugasan.dpl.data') }}" method="POST" target="_blank">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <select class="form-control select2bs4 w-100 jk" id="jns" name="jenis_kelamin" required>
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
