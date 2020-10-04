@includeWhen(!empty($widget['wrapper']), 'backpack::widgets.inc.wrapper_start')

    <div class="card">
      <div class="card-body p-3 d-flex align-items-center"><i class="la la-check bg-primary p-3 font-2xl mr-3"></i>
        <div>
          <div class="text-value-sm text-primary">{!! $widget['count'] !!}</div>
          <div class="text-muted text-uppercase font-weight-bold small">{!! $widget['name'] !!}</div>
          <small class="text-muted">{!! $widget['description'] !!}</small>
        </div>
      </div>
    </div>

@includeWhen(!empty($widget['wrapper']), 'backpack::widgets.inc.wrapper_end')