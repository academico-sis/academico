<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

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
                <div class="title">
                    @yield('message')
                </div>
            </div>
        </div>

        @if(app()->bound('sentry') && app('sentry')->getLastEventId())
            <script src="https://browser.sentry-cdn.com/5.7.1/bundle.min.js" integrity="sha384-KMv6bBTABABhv0NI+rVWly6PIRvdippFEgjpKyxUcpEmDWZTkDOiueL5xW+cztZZ" crossorigin="anonymous"></script>
            <script>
                Sentry.init({ dsn: '{{ config('settings.sentry_laravel_dsn') }}' });
                Sentry.showReportDialog({
                    eventId: '{{ Sentry::getLastEventID() }}',
                    lang: 'es',
                });
            </script>
        @endif

    </body>
</html>
