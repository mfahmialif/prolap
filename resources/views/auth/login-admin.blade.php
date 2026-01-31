<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Admin PPL/PKL</title>
    <link rel="icon" type="image/png" href="{{ asset('landingpage/assets/images/logo-ponpes-icon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/assets_login_admin/css/style.css') }}" />
    <style>
        .alert {
            margin-bottom: 20px;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-in-container">
            <form action="{{ route('admin.login') }}" method="POST">
                @csrf
                <img src="{{ asset('assets/assets_login_admin/img/logo transparent.png') }}" alt="Logo PPL/PKL"
                    style="width: 60px; height: 60px; margin-bottom: 20px" />
                <h1>Masuk</h1>
                <input type="text" name="username" placeholder="Username" required />
                <input type="password" name="password" placeholder="Password" required />
                <div class="sub-title">Klik <b>masuk</b> untuk melanjutkan!</div>
                <button class="btn-login">Masuk</button>
            </form>
        </div>

        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <img src="{{ asset('assets/assets_login_admin/img/kkn.png') }}" alt="PPL/PKL Image"
                        style="width: 250px; height: 250px; margin-top: -42px" />
                    <h2>Assalamu'alaikum</h2>
                    <p>Selamat datang di website PPL/PKL</p>
                    <button class="ghost" id="signIn">Kembali</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    @if ($errors->any())
    <script>
        swal({
            title: "Warning",
            text: "username dan password salah",
            icon: "warning",
            button: "Ok",
        });
    </script>
    @endif

</body>
</body>

</html>
