<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de notas del curso</title>
    <style>
        table, tr, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        .t14 {
            font-size: 14pt;
        }

        .t12 {
            font-size: 14pt;
        }

        .t14 {
            font-size: 14pt;
        }

        .t14b {
            font-size: 14pt;
            font-weight: bold;
        }

        .t16b {
            font-size: 16pt;
            font-weight: bold;
        }

        .names-col {
            width: 5cm;
        }

        .criteria-col {
            width: 2cm;
            text-align: center;
        }
    </style>
</head>
<body>

<table style="width: 27cm;">
    <tr>
        <td>
            <p class="t16b">{{ Str::upper('Reporte de notas') }}</p>
        </td>
        <td>
            <p class="t14b">{{ Str::upper($course->name) }}</p>
            <p class="t12" style="text-align: center;">Periodo pedagogico: {{ $course->period->name }}</p>
            <p class="t12" style="text-align: center;">Módulo: {{ $course->level->name }}</p>
        </td>
    </tr>
</table>

<div class="row">

    <table class="table" style="width: 27cm;">
        <tr>
            <td class="names-col"></td>
            @php $total = 0; @endphp
            @foreach ($course->grade_types->sortBy('id') as $grade_type)
                <td class="criteria-col">
                    ({{$grade_type->category->name}})<br> <strong>{{$grade_type->name}}</strong>
                </td>
                @php $total += $grade_type->total; @endphp
            @endforeach
            <td><strong>@lang('TOTAL')</strong></td>
            <td><strong>@lang('Result')</strong></td>
        </tr>

        @foreach ($enrollments as $enrollment)
            @php $student_total = 0; @endphp
            <tr>
                <td class="names-col">{{ $enrollment->student_name }}</td>

                @foreach ($course->grade_types->sortBy('id') as $grade_type)
                    <td class="criteria-col">
                        @foreach($enrollment->grades->where('grade_type_id', $grade_type->id) as $grade)
                            @php $student_total += $grade->grade; @endphp
                            {{ $grade->grade }}
                        @endforeach
                    </td>
                @endforeach

                <td>
                    <strong>{{ $student_total }} / {{ $total }}</strong>
                </td>

                <td>
                    {{ $enrollment->result_name }}
                </td>

            </tr>
        @endforeach
    </table>

</div>

<div style="margin-top: 1cm;">
    <span style="text-align: left; font-size: 10pt; font-weight: bold;">
        PROFESOR ENCARGADO DEL MÓDULO
    </span>
    <br>
    <span style="text-align: left; font-size: 11pt;">
        {{ $course->teacher->name }}
    </span>
    <p class="t14">En Loja, el {{ \Carbon\Carbon::now(config('settings.courses_timezone'))->isoFormat('d \d\e\ MMMM \d\e Y') }}</p>
</div>


</body>
</html>