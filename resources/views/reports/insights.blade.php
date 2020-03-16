<div class="row">

<div class="col-sm-6 col-md-4">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-star-o bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ $total_enrollment_count }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Enrollments')</div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-md-4">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-credit-card bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ $paid_enrollment_count }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Paid Enrollments')</div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-md-4">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-user bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ $students_count }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('students')</div>
            </div>
        </div>
    </div>
</div>

</div>