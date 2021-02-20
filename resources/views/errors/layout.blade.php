<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Nunito&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 36px;
                padding: 20px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <img src="{{ asset('logo.png') }}" alt="Academico logo">

                <div class="title">
                    @yield('message')
                </div>

                <p>@lang("If you don't understand why you're seeing this error, please report it so it can be fixed")</p>
                <p>@lang('Send an email and describe your issue:')
                    <a href="mailto:incoming+thdebay-academico-site-17656010-issue-@incoming.gitlab.com">incoming+thdebay-academico-site-17656010-issue-@incoming.gitlab.com</a>
                </p>

                <a href="{{ app('router')->has('home') ? route('home') : url('/') }}">
                    <button class="bg-transparent text-grey-darkest font-bold uppercase tracking-wide py-3 px-6 border-2 border-grey-light hover:border-grey rounded-lg">
                        {{ __('Go Home') }}
                    </button>
                </a>
            </div>
        </div>
    </body>
</html>
