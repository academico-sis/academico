<div class="col-6 col-lg-3">
    <div class="card">
        <div class="card-body p-3 d-flex align-items-center"><i class="la la-bell bg-danger p-4 font-2xl mr-3"></i>
            <div>
                <div class="text-value-sm text-danger">$ <span id="balanceTotal"></span></div>
                <div class="text-muted text-uppercase font-weight-bold small">{{ __('Pending balance') }}</div>
            </div>
        </div>
    </div>
</div>

@push('after_scripts')
    {{-- compute balance total --}}
    <script>
        jQuery(document).ready(function($) {
            crud.table.on('draw.dt', function () {
                let searchParams = new URLSearchParams(window.location.search)
                let periodId = searchParams.get('period_id');
                $.post(
                    "/getEnrollmentBalance",
                    {periodId},
                    data => $("#balanceTotal").html(data)
                );
            });
        });
    </script>
@endpush
