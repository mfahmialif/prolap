{{-- Modal Ganti Password --}}
<form action="" id="form-setting" method="POST">
    @csrf
    @method('PUT')
    <div class="modal fade" id="modal-password">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ganti Password</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_edit" id="id_edit" value="{{ Auth::user()->id }}">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" id="username"
                            placeholder="Masukkan Username" value="{{ Auth::user()->username }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" id="password"
                            placeholder="Masukkan Password Baru">
                    </div>
                    <div class="form-group">
                        <label for="password_confirm">Konfirmasi
                            Password</label>
                        <input type="password" name="password_confirm" class="form-control" id="password_confirm"
                            placeholder="Masukkan Konfirmasi Password">
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="button_submit_setting">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('script')
    <script>
        $(document).ready(function() {
            $("#form-setting").validate({
                rules: {
                    password: {
                        required: true
                    },
                    password_confirm: {
                        required: true,
                        equalTo: "#password"
                    }
                },
                messages: {
                    password: {
                        required: "Password is required"
                    },
                    password_confirm: {
                        required: "Confirm Password is required",
                        equalTo: "Passwords do not match"
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('pl-2 invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            $('#form-setting').submit(function(e) {
                e.preventDefault();

                if ($(this).valid()) {
                    let fd = new FormData(this);

                    $.ajax({
                        type: "POST",
                        url: "{{ route('peserta.dashboard.changePassword') }}",
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            console.log(response);
                            if (response.message == 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.data,
                                    timer: 1000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'error',
                                    text: response.data,
                                    timer: 1000,
                                    showConfirmButton: false
                                });
                            }
                            $('#modal-password').modal('toggle');
                            $('#form-setting').trigger('reset');
                        }
                    });
                }
            });
        });
    </script>
@endpush