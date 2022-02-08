<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <meta name="google-signin-client_id" content="572750383529-8ujv1oagblneqh0jdt4tk0qtcpmu450k.apps.googleusercontent.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ url('/assets') }}/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="{{ url('/assets') }}/dist/css/adminlte.min.css">
    <script>
        _PAGE_URL = "{{ url('/') }}"
    </script>
</head>
<body class="hold-transition login-page">
    <div id="app">
        <app-login></app-login>
    </div>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <script src="{{ url('/assets') }}/plugins/jquery/jquery.min.js"></script>
    <script src="{{ url('/assets') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ url('/assets') }}/dist/js/adminlte.min.js"></script>
    <script src="{{ url('/public') }}/js/app.js?ver={{ strtotime(date('Y-m-d H:i:s')) }}"></script>
</body>
</html>