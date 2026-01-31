<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Login</title>
    <!-- Favicons -->
    <link href="{{ asset('img/logo.ico') }}" rel="icon">
    <link href="{{ asset('img/logo.ico') }}" rel="apple-touch-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css" />
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oxygen" />
    <link rel="stylesheet" href="{{ asset('login-admin/style.css') }}" />
</head>

<body>
    <!-- partial:index.partial.html -->
    <link href="https://fonts.googleapis.com/css?family=Oxygen:400,300,700" rel="stylesheet" type="text/css" />
    <link href="https://code.ionicframework.com/ionicons/1.4.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <div class="signin cf">
        <img class="logo" src="{{ asset('login-admin/logo.png') }}" alt="" />
        <!-- <div class="avatar"></div> -->

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="inputrow">
                <input type="text" id="username" name="username" placeholder="Username" />
                <label class="ion-person" for="username"></label>
            </div>
            <div class="inputrow">
                <input type="password" id="password" name="password" placeholder="Password" />
                <label class="ion-locked" for="password"></label>
            </div>
            <input type="checkbox" name="remember" id="remember" />
            <label class="radio" for="remember">Stay Logged In</label>
            <input type="submit" value="Login" />
        </form>
    </div>
    <!-- partial -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="{{ asset('login-admin/script.js') }}"></script>
</body>

</html>