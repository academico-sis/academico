@if(isset($allow_year_selection) && $allow_year_selection)
    <label for="dropdownMenuLink">{{ __('Year') }}</label>

    <div class="btn-group" style="padding-right: 2rem">
        <div class="dropdown show">
            <a class="btn btn-secondary dropdown-toggle" id="dropdownMenuLink" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            @if ($selected_period['type'] === 'year')
                {{ $selected_period['value'] }}
            @else
                --
            @endif
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                @foreach ($allYears as $year)
                    <li><a class="dropdown-item" href="{{ url()->current() }}/?year={{ $year->id }}">{{ $year->name }}</a></li>
                @endforeach
            </div>
        </div>
    </div>
@endif


<label for="dropdownMenuLink">{{ __('Period') }}</label>
<div class="btn-group">
    <div class="dropdown show">
        <a class="btn btn-secondary dropdown-toggle" id="dropdownMenuLink" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        @if ($selected_period['type'] === 'period')
            {{ $selected_period['value'] }}
            @else
                --
            @endif
        </a>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            @foreach ($periods as $period)
                <li><a class="dropdown-item" href="{{ url()->current() }}/?period={{ $period->id }}">{{ $period->name }}</a></li>
            @endforeach
        </div>
    </div>
</div>

@push('before_styles')
    <style>
        .dropdown-menu {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
@endpush
