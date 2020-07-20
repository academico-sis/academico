<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
@if (config('backpack.base.meta_robots_content'))
<meta name="robots" content="{{ config('backpack.base.meta_robots_content', 'noindex, nofollow') }}">
@endif

{{-- Encrypted CSRF token for Laravel, in order for Ajax requests to work --}}
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link rel="stylesheet" href="https://cdn.materialdesignicons.com/2.5.94/css/materialdesignicons.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css">
<title>
  {{ isset($title) ? $title.' :: '.config('backpack.base.project_name').' Admin' : config('backpack.base.project_name').' Admin' }}
</title>


</head>
<body>
    <section class="section">
        <div class="container">
            <h1 class="title">{{ trans('backpack::base.register') }}</h1>
            <div id="app">
                <register-component :institutions="{{ \App\Models\Institution::all()->pluck('name') }}"></register-component>
            </div>
        </div>
    </section>

<script src="{{ mix('js/register.js') }}"></script>
</body>
</html>
