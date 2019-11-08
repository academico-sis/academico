<div class="row">

<div class="col-sm-6 col-md-3">
    <div class="card">
        <div class="card-body">
            <div class="h1 text-muted text-right mb-4"><i class="fa fa-calendar"></i></div>
            <div class="text-value">{{ $period->name }}</div><small class="text-muted text-uppercase font-weight-bold">@lang('Current Period')</small>
        </div>
    </div>
</div>

<div class="col-sm-6 col-md-3">
    <div class="card">
        <div class="card-body">
            <div class="h1 text-muted text-right mb-4"><i class="fa fa-star-o"></i></div>
            <div class="text-value">{{ $total_enrollment_count }}</div><small class="text-muted text-uppercase font-weight-bold">@lang('Enrollments')</small>
        </div>
    </div>
</div>

<div class="col-sm-6 col-md-3">
    <div class="card">
        <div class="card-body">
            <div class="h1 text-muted text-right mb-4"><i class="fa fa-star-o"></i></div>
            <div class="text-value">{{ $paid_enrollment_count }}</div><small class="text-muted text-uppercase font-weight-bold">@lang('paid')</small>
            <div class="progress progress-xs mt-3 mb-0">
                <div class="progress-bar bg-info" role="progressbar" style="width: {{ 100*$paid_enrollment_count/max($total_enrollment_count, 1) }}%" aria-valuenow="{{ 100*$paid_enrollment_count/max($total_enrollment_count, 1) }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-md-3">
    <div class="card">
        <div class="card-body">
            <div class="h1 text-muted text-right mb-4"><i class="fa fa-user"></i></div>
            <div class="text-value">{{ $students_count }}</div><small class="text-muted text-uppercase font-weight-bold">@lang('students')</small>
        </div>
    </div>
</div>

</div>