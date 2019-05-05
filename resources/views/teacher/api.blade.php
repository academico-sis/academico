@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>@lang('Api Token')</h1>
</section>
@endsection


@section('content')

<div class="row" id="app">
    <div class="col-md-12">
    @if(isset($user->api_token))
        <p>@lang('The server URL is:')
            <span style='font-family: mono'>{{ url('/api') }}/</span>
        </p>

        <p>@lang('Your token is:')
            <span style='font-family: mono'>{{ $user->api_token }}</span>
        </p>

        <div>
            {!! QrCode::size(300)->generate($user->api_token); !!}
        </div>
    @endif


        <button class='btn btn-primary' onclick="generateToken()">
            @lang('Generate a new token')
        </button>
    </div>
</div>
@endsection


@section('after_scripts')
    <script src="/js/app.js"></script>

    <script>
        function generateToken()
        {
            axios.post('/apitoken').then(response => {
                location.reload();
            });
        }
    </script>
@endsection