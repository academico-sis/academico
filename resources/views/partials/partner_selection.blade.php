<div class="btn-group">
    <div class="dropdown show">
    <a class="btn btn-secondary dropdown-toggle" id="dropdownMenuLink" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $selected_partner?->name ?? __('all')}}</a>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            <li><a class="dropdown-item" href="{{ url()->current() }}?partner=">{{ __('all') }}</a></li>
            @foreach ($partners as $partner)
                <li><a class="dropdown-item" href="{{ url()->current() }}?partner={{ $partner->id }}">{{ $partner->name }}</a></li>
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
