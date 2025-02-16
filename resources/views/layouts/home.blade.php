<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <!-- <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,600" rel="stylesheet" type="text/css"> -->
        
        {{-- <link rel="stylesheet" href="{{ asset('css/vendor.css'}"> --}}
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <link href="{{ asset('libs/tiny-slider/tiny-slider.css') }}" rel="stylesheet">
        <link href="{{ asset('libs/tobii/css/tobii.min.css') }}" rel="stylesheet">
        <link href="{{ asset('libs/%40mdi/font/css/materialdesignicons.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('libs/%40iconscout/unicons/css/line.css') }}" type="text/css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/custom/style.css') }}">
        <!-- Styles -->
        <style>
            /* body {
                min-height: 100vh;
                background-color: #243949;
                color: #nnn;
                background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.12'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            }
            .navbar-default {
                background-color: transparent;
                border: none;
            }
            .navbar-static-top {
                margin-bottom: 19px;
            }
            .navbar-default .navbar-nav>li>a {
                color: #fff;
                font-weight: 600;
                font-size: 15px
            }
            .navbar-default .navbar-nav>li>a:hover{
                color: #ccc;
            }
            .navbar-default .navbar-brand {
                color: #ccc;
            } */
        </style>
    </head>

    <body>
        @include('layouts.partials.home_header2')
        @yield('content')
        @include('layouts.partials.javascripts')
       
    <!-- Scripts -->
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- SLIDER -->
    <script src="{{ asset('libs/tiny-slider/min/tiny-slider.js') }}"></script>
    <!-- Lightbox -->
    <script src="{{ asset('libs/tobii/js/tobii.min.js') }}"></script>
    <!-- Main Js -->
    <script src="{{ asset('libs/feather-icons/feather.min.js') }} assets"></script>
    <script src="{{ asset('js/custom/plugins.init.js') }}"></script><!--Note: All init js like tiny slider, counter, countdown, maintenance, lightbox, gallery, swiper slider, aos animation etc.-->
    <script src="{{ asset('js/custom/app.js') }}"></script><!--Not
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>
    @yield('javascript')
    </body>
</html>