@extends('layouts.home')
@section('title', config('app.name', 'ultimatePOS'))

@section('content')
    {{-- <style type="text/css">
        .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
                margin-top: 10%;
            }
        .title {
                font-size: 84px;
            }
        .tagline {
                font-size:25px;
                font-weight: 300;
                text-align: center;
            }

        @media only screen and (max-width: 600px) {
            .title{
                font-size: 38px;
            }
            .tagline {
                font-size:18px;
            }
        }
    </style> --}}
    {{-- <div class="title flex-center" style="font-weight: 600 !important;">
        {{ config('app.name', 'ultimatePOS') }}
    </div>
    <p class="tagline">
        {{ env('APP_TITLE', '') }}
    </p> --}}
    <section class="bg-half-170 d-table w-100 overflow-hidden" id="home">
        <div class="container">
            <div class="row align-items-center pt-5">
                <div class="col-lg-7 col-md-6">
                    <div class="title-heading">
                        <h1 class="heading mb-3">Everything you <br> need to do <br> better work</h1>
                        <p class="para-desc text-muted">Launch your campaign and benefit from our expertise on designing and managing conversion centered bootstrap v5 html page.</p>
                        <div class="mt-4 pt-2">
                            <a href="#!" target="_blank" class="btn btn-primary">Buy Now<span class="badge rounded-pill bg-danger ms-2">v4.9.0</span></a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-md-6 mt-4 pt-2 mt-sm-0 pt-sm-0">
                    <div class="classic-saas-image position-relative">
                        <div class="bg-saas-shape position-relative">
                            <img src="{{ asset('img/landing.png') }}" class="mx-auto d-block" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
            