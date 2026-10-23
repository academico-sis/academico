<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Result sheet') }}</title>
    @include('pdf.partials.styles')
</head>
<body>
    @include('pdf.partials.header')

    <h1>{{ __('Result sheet') }}</h1>

    <table class="details">
        <tr>
            <td class="label">{{ __('Student') }}</td>
            <td><strong>{{ $enrollment->student_name }}</strong></td>
        </tr>
        <tr>
            <td class="label">{{ __('Course') }}</td>
            <td>{{ $course->name }}</td>
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

    @if ($gradeCategories)
        <table class="grid">
            <thead>
                <tr>
                    <th>{{ __('Grades') }}</th>
                    <th class="number" style="width: 35mm;">{{ __('Grade') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($gradeCategories as $category)
                    @if ($category['name'])
                        <tr class="category">
                            <td>{{ $category['name'] }}</td>
                            <td class="number">{{ $category['grade'] }} / {{ $category['total'] }}</td>
                        </tr>
                    @endif
                    @foreach ($category['grades'] as $grade)
                        <tr>
                            <td>{{ $grade['name'] }}</td>
                            <td class="number">{{ $grade['grade'] ?? '-' }} / {{ $grade['total'] }}</td>
                        </tr>
                    @endforeach
                @endforeach
                <tr class="total">
                    <td>{{ __('Total') }}</td>
                    <td class="number">{{ $grandTotal['grade'] }} / {{ $grandTotal['total'] }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    @if ($skillTypes)
        <table class="grid">
            <thead>
                <tr>
                    <th>{{ __('Skills') }}</th>
                    <th style="width: 50mm;">{{ __('Evaluation') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($skillTypes as $skillType)
                    @if ($skillType['name'])
                        <tr class="category">
                            <td colspan="2">{{ $skillType['name'] }}</td>
                        </tr>
                    @endif
                    @foreach ($skillType['skills'] as $skill)
                        <tr>
                            <td>{{ $skill['name'] }}</td>
                            <td>{{ $skill['scale'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endif

    <table class="grid">
        <tr class="total">
            <td>{{ __('Result') }}</td>
            <td style="width: 50mm;">{{ $enrollment->result?->result_name?->name ?? '-' }}</td>
        </tr>
        @if ($enrollment->result?->comments?->isNotEmpty())
            <tr>
                <td colspan="2">
                    <span class="muted">{{ __('Comments') }}</span><br>
                    @foreach ($enrollment->result->comments as $comment)
                        {!! nl2br(e($comment->body)) !!}<br>
                    @endforeach
                </td>
            </tr>
        @endif
    </table>

    <p class="place">{{ $place }}</p>
</body>
</html>
