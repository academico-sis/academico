<label for="dropdownMenuLink">@lang('Period')</label>
<div class="btn-group">
    <div class="dropdown show">
    <a class="btn btn-secondary dropdown-toggle" id="dropdownMenuLink" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $selected_period->name }}</a>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            @foreach ($periods as $period)
                <li><a class="dropdown-item" href="{{ url()->current() }}/?period={{ $period->id }}">{{ $period->name }}</a></li>
            @endforeach
        </div>
    </div>
</div>