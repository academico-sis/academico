<div class="row">
    
    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-black"><i class="fa fa-calendar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">@lang('Current Period')</span>
                <span class="info-box-number">{{ $period->name }}</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-star-o"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">@lang('Enrollments')</span>
                <span class="info-box-number">{{ $total_enrollment_count }}</span>
                <div class="progress">
                    <div class="progress-bar" style="background-color: lightcoral; width: {{ 100*$paid_enrollment_count/max($total_enrollment_count, 1) }}%"></div>
                </div>
                <span class="progress-description">
                    {{ $paid_enrollment_count }} @lang('paid')
                </span>
            </div>
        </div>
    </div>
    
    
    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-user-o"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">@lang('students')</span>
                <span class="info-box-number">{{ $students_count }}</span>
            </div>
        </div>
    </div>
    
    
</div>
