<div class="d-inline-block col-sm-6 col-md-3">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center">
            <i class="la {{ $widget['icon'] }} bg-primary p-3 font-2xl mr-3"></i>
            <div>
                <a href="/student?lead_type_id={{ $widget['leadTypeId'] }}">
                    <div class="text-value-sm text-primary">{{ $widget['studentCount'] }}</div>
                    <div class="text-muted text-uppercase font-weight-bold small">{{ $widget['name'] }}</div>
                    <small class="text-muted">{{ $widget['description'] }}</small>
                </a>
            </div>
        </div>
    </div>
</div>
