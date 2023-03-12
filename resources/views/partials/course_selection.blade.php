<label for="courseDropdown">{{ __('Course') }}</label>

<div class="btn-group">
    <div class="dropdown show">
    <a class="btn btn-secondary dropdown-toggle" id="courseDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $course?->name ?? "--" }}</a>
        <div class="dropdown-menu" aria-labelledby="courseDropdown">
            @foreach ($courses as $course)

                @php
                    $baseUrl = url()->current();
                    $params = ['courseId' => $course->id];
                    $urlWithParams = $baseUrl = $baseUrl . '?' . http_build_query(array_merge(request()->query(), $params));;
                @endphp

                <li><a class="dropdown-item" href="{{ $urlWithParams }}">{{ $course->name }}</a></li>
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
