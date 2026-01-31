<div class="card card-warning">
    <div class="card-header">
        Sinkronisasi SIMKEU
        <div class="card-tools">
            <button type="button" class="btn btn-tool" id="card_refresh_simkeu" data-card-widget="card-refresh"
                data-source="{{ url()->current() }}" data-source-selector="#card_refresh_content_simkeu"
                data-load-on-init="false">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                <i class="fas fa-expand"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
        <!-- /.card-tools -->
    </div>

    <div class="card-body" id="card_refresh_content_simkeu">
        <form id="form_simkeu" action="{{ route('admin.setting.simkeu') }}" method="post">
            @csrf
            <p>Klik tombol di bawah untuk SINKRONISASI SIMKEU</p>
            <button class="btn btn-warning w-100" id="form_submit_simkeu">Sinkronkan</button>
        </form>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            initSimkeu();

            $('#card_refresh_simkeu').on('overlay.removed.lte.cardrefresh', function() {
                initSimkeu();
            });
        });

        function cardRefreshSimkeu() {
            var cardRefresh = document.querySelector('#card_refresh_simkeu');
            cardRefresh.click();
        }

        function initSimkeu() {
            $('#form_simkeu').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.setting.simkeu') }}",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('#form_submit_simkeu').attr('disabled', true);
                        $('#form_submit_simkeu').html('Loading...');
                    },
                    complete: function() {
                        $('#form_submit_simkeu').attr('disabled', false);
                        $('#form_submit_simkeu').html('Simpan');
                    },
                    success: function(response) {
                        swalToast(response.message, response.data);
                        cardRefreshSimkeu();
                        console.log(response);
                    }
                });
            });

        }
    </script>
@endpush
