
<div class="row">

@if(isset($course->teacher))
<div class="col-md-3">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="la la-user bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ $course->teacher->name }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Teacher')</div>
            </div>
        </div>
    </div>
</div>
@endif

@if(isset($course->rhythm) || isset($course->level))
<div class="col-md-3">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="la la-building-o bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ $course->name ?? '' }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">{{ $course->rhythm->name ?? '-' . ' ' . $course->level->name ?? '-' }}</div>
            </div>
        </div>
    </div>
</div>
@endif

@if(isset($course->course_times))
<div class="col-md-3">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="la la-clock-o bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ $course->course_times }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Schedule')</div>
            </div>
        </div>
    </div>
</div>
@endif

@if(isset($course->room))
<div class="col-md-3">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="la la-clock-o bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ $course->room->name }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Room')</div>
            </div>
        </div>
    </div>
</div>
@endif
    
</div>