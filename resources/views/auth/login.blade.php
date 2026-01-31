<!DOCTYPE html>
<html>

<>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login User PPL/PKL</title>
    <link rel="icon" type="image/png" href="{{ asset('landingpage/assets/images/logo-ponpes-icon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('login-peserta/style.css') }}">
    </head>

    <body>
        <div class="wrapper">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <h2>Login</h2>
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        @foreach ($errors->all() as $item)
                            {{ $item }}
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="input-field">
                    <input type="text" id="username" name="username" required>
                    <label>Masukkan Username / NIM</label>
                </div>
                <div class="input-field">
                    <input type="password" id="password" name="password" required>
                    <label>Masukkan Password</label>
                </div>
                <div class="forget">
                    <label for="remember">
                        <input type="checkbox" id="remember">
                        <p>Remember me</p>
                    </label>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="bg-success"><i class="fa-solid fa-right-to-bracket me-2"></i>Log In</button>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <button type="button" class="bg-danger"
                            onclick="location.href='{{ route('root') }}'"><i class="fa-solid fa-arrow-left me-2"></i></i>Kembali</button>
                    </div>
                </div>

            </form>
        </div>

        <script>
            document.getElementById("username").focus();
        </script>
    </body>

</html>
