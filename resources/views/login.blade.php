<!DOCTYPE html>
<html lang="en" data-ng-app="app">

<head>
    <title>{{ config('app.name') }} - Login </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container py-4">
        <div class="text-center">

            <div class="auth-brand text-center d-block mb-20">
                <img class="brand-img" src="{{ asset('img/login-logo.png') }}">
            </div>
            <h3> Login </h3>

            <a href="{{ $loginUrl }}" class="btn btn-lg btn-primary "><i class="fa fa-facebook fa-fw"></i> Login with Detikconnect</a>
        </div>
    </div>
</body>

</html>