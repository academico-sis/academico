<h2>Détail des heures - {{ $monthName }} {{ $year }}</h2>

@foreach ($teachers as $teacher)
    <h3>{{ $teacher->name }}</h3>
    <ul>
        <li>@lang('Face-to-face') : {{ number_format($teacher->plannedHoursInPeriod($start, $end), 2, '.', ',') }} h</li>
        <li>@lang('Remote') : {{ number_format($teacher->plannedRemoteHoursInPeriod($start, $end), 2, '.', ',') }} h</li>
        <li><strong>@lang('Total:') {{ number_format($teacher->plannedHoursInPeriod($start, $end) + $teacher->plannedRemoteHoursInPeriod($start, $end), 2, '.', ',') }} h</strong></li>
    </ul>
@endforeach
