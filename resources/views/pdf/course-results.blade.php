<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Course results') }}</title>
    @include('pdf.partials.styles')
</head>
<body>
    @include('pdf.partials.header')

    <h1>{{ __('Course results') }}</h1>

    <table class="details">
        <tr>
            <td class="label">{{ __('Course') }}</td>
            <td><strong>{{ $course->name }}</strong></td>
        </tr>
        @if ($course->level)
            <tr>
                <td class="label">{{ __('Level') }}</td>
                <td>{{ $course->level->name }}</td>
            </tr>
        @endif
        @if ($course->period)
            <tr>
                <td class="label">{{ __('Period') }}</td>
                <td>{{ $course->period->name }}</td>
            </tr>
        @endif
        @if ($course->teacher)
            <tr>
                <td class="label">{{ __('Teacher') }}</td>
                <td>{{ $course->teacher->name }}</td>
            </tr>
        @endif
    </table>

    <table class="grid">
        <thead>
            <tr>
                <th>{{ __('Student') }}</th>
                @foreach ($gradeTypes as $gradeType)
                    <th class="number">
                        {{ $gradeType->name }}
                        @if ($gradeType->category)
                            <br><span class="muted">{{ $gradeType->category->name }}</span>
                        @endif
                    </th>
                @endforeach
                @if ($gradeTypes->isNotEmpty())
                    <th class="number">{{ __('Total') }}</th>
                @endif
                <th>{{ __('Result') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['student'] }}</td>
                    @foreach ($gradeTypes as $gradeType)
                        <td class="number">{{ $row['grades'][$gradeType->id] ?? '-' }}</td>
                    @endforeach
                    @if ($gradeTypes->isNotEmpty())
                        <td class="number"><strong>{{ $row['total']['grade'] }} / {{ $row['total']['total'] }}</strong></td>
                    @endif
                    <td>{{ $row['result'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $gradeTypes->count() + ($gradeTypes->isNotEmpty() ? 3 : 2) }}">{{ __('No enrollments') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="place">{{ $place }}</p>
</body>
</html>
