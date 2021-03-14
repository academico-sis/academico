<div class="card">
    <div class="card-body p-3 d-flex align-items-center"><i class="la la-exclamation-triangle bg-danger p-3 font-2xl mr-3"></i>
        <div>
            <div class="text-value-sm text-danger">{{ $remoteVolume }}h</div>
            <div class="text-muted text-uppercase font-weight-bold small">@lang('Remote hours')</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-3 d-flex align-items-center"><i class="la la-exclamation-triangle bg-danger p-3 font-2xl mr-3"></i>
        <div>
            <div class="text-value-sm text-danger">{{ $volume }}h</div>
            <div class="text-muted text-uppercase font-weight-bold small">@lang('Presential hours')</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-3 d-flex align-items-center"><i class="la la-exclamation-triangle bg-danger p-3 font-2xl mr-3"></i>
        <div>
            <div class="text-value-sm text-danger">{{ $totalVolume }}h</div>
            <div class="text-muted text-uppercase font-weight-bold small">@lang('Total Hours')</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-3 d-flex align-items-center"><i class="la la-exclamation-triangle bg-danger p-3 font-2xl mr-3"></i>
        <div>
            <div class="text-value-sm text-danger">{{ $teacher->plannedHoursInPeriod($selected_period->start, $selected_period->end) }}h</div>
            <div class="text-muted text-uppercase font-weight-bold small">@lang('Hours on schedule')</div>
        </div>
    </div>
</div>
