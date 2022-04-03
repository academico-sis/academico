<div class="row">

<div class="col-sm-6 col-md-4">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="la la-star-o bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ $total_enrollment_count }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Enrollments')</div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-md-4">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="la la-credit-card bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ $paid_enrollment_count }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('Paid Enrollments')</div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-md-4">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="la la-user bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-primary">{{ $students_count }}</div>
                <div class="text-muted text-uppercase font-weight-bold small">@lang('students')</div>
            </div>
        </div>
    </div>
</div>

</div>


<div class="row">

    @if($unassigned_events > 0 && backpack_user()->can('hr.manage'))
        <div class="col-md-4 col-sm-6">
            <div class="card">
                <div class="card-body p-3 d-flex align-items-center"><i class="la la-exclamation-triangle bg-danger p-3 font-2xl mr-3"></i>
                    <div>
                        <div class="text-value-sm text-danger">{{ $unassigned_events }}</div>
                        <div class="text-muted text-uppercase font-weight-bold small">@lang('Classes without teacher')</div>
                    </div>
                </div>
                <div class="card-footer px-3 py-2"><a class="btn-block text-muted d-flex justify-content-between align-items-center" href='event?unassigned=true'><span class="small font-weight-bold">@lang('View')</span><i class="la la-angle-right"></i></a></div>
            </div>
        </div>
    @endif

    <div class="col-md-4 col-sm-6">
        <div class="card">
            <div class="card-body p-3 d-flex align-items-center"><i class="la la-calendar bg-danger p-3 font-2xl mr-3"></i>
                <div>
                    <div class="text-value-sm text-danger">{{ $pending_attendance }}</div>
                    <div class="text-muted text-uppercase font-weight-bold small">@lang('Pending Attendance')</div>
                </div>
            </div>
            <div class="card-footer px-3 py-2"><a class="btn-block text-muted d-flex justify-content-between align-items-center" href="/attendance"><span class="small font-weight-bold">@lang('View')</span><i class="la la-angle-right"></i></a></div>
        </div>
    </div>
</div>
