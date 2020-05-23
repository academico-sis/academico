<div class="brand-card">
    <div class="brand-card-body">
        <div>
            <div class="text-value">{{ $currentPeriod->name }}</div>
            <div class="text-uppercase text-muted small">@lang('Current Period')</div>
        </div>
        <div>
            <div class="text-value">{{ $enrollmentsPeriod->name }}</div>
            <div class="text-uppercase text-muted small">@lang('Enrollments Period')</div>
        </div>
        @if (backpack_user()->hasPermissionTo('enrollments.edit'))
        <div>
            <div class="text-value"><a class="btn" href="{{ route('get-default-periods-screen') }}"><i class="la la-edit"></i></a></div>
            <div class="text-uppercase text-muted small">@lang('Edit')</div>
        </div>
        @endif
    </div>
</div>