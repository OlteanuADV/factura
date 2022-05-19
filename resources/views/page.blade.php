<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ url('/assets') }}/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <script>
        _PAGE_URL = "{{ url('/') }}"
    </script>
</head>
<body class="hold-transition">
    <div id="app"><app-view></app-view></div>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script src="{{ url('/assets') }}/plugins/jquery/jquery.min.js"></script>
    <script src="{{ url('/assets') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ url('/assets') }}/dist/js/adminlte.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/ro_RO/sdk.js#xfbml=1&version=v13.0&appId=677979423438686&autoLogAppEvents=1" nonce="2p7Davuj"></script>
    <script>
        window.fbAsyncInit = function() {
          FB.init({
            appId      : '{{ env('FACEBOOK_APP_ID') }}',
            cookie     : true,
            xfbml      : true,
            version    : 'v13.0'
          });
            
          FB.AppEvents.logPageView();   
            
        };
      
        (function(d, s, id){
           var js, fjs = d.getElementsByTagName(s)[0];
           if (d.getElementById(id)) {return;}
           js = d.createElement(s); js.id = id;
           js.src = "https://connect.facebook.net/en_US/sdk.js";
           fjs.parentNode.insertBefore(js, fjs);
         }(document, 'script', 'facebook-jssdk'));
      </script>
    <script src="{{ url('/public') }}/js/app.js?ver={{ strtotime(date('Y-m-d H:i:s')) }}"></script>
</body>
</html>