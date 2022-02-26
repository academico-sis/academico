
<h2>
    {{ $widget['course']?->name ?? '' }}<br>
    <span class="text-muted text-uppercase font-weight-bold small">{{ $widget['course']?->rhythm->name ?? '-' . ' ' . $widget['course']?->level->name ?? '-' }}</span>
    |
    @if ($widget['course']->start_date && $widget['course']->end_date) <span class="text-muted small">{{ $widget['course']->formatted_start_date }} - {{ $widget['course']->formatted_end_date }}</span>@endif
</h2>

<div class="row">

@if(isset($widget['course']->teacher))
<div class="col-md-3">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="la la-user bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ $widget['course']->teacher->name }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Teacher')</div>
            </div>
        </div>
    </div>
</div>
@endif


@if(isset($widget['course']->course_times))
<div class="col-md-3">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="la la-clock-o bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ $widget['course']->course_times }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Schedule')</div>
            </div>
        </div>
    </div>
</div>
@endif

@if(isset($widget['course']->room))
<div class="col-md-3">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="la la-clock-o bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ $widget['course']->room->name }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Room')</div>
            </div>
        </div>
    </div>
</div>
@endif

@if($widget['course']->books->count() > 0)
<div class="col-md-3">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="la la-user bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">
                    @foreach ($widget['course']->books as $book)
                        {{ $book->name }}
                    @endforeach
                </div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Book')</div>
            </div>
        </div>
    </div>
</div>
@endif

</div>
