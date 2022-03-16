<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de notas</title>
    <style>
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
    </style>
</head>
<body>

<div style="text-align: center; margin-bottom: 1cm;">
    <img src="{{ storage_path('logo.jpg') }}" alt="Logo">
</div>

<div class="row">

<div>

    <table style="width: 18cm;">
        <tr>
            <td style="text-align: center;"><span class="t16b">REPORTE DE NOTAS DE:</span></td>
            <td class="t14">{{ Str::upper($enrollment->student->lastname) }} {{ $enrollment->student->firstname }}</td>
        </tr>

        <tr>
            <td style="text-align: center;"><span class="t16b">MÓDULO:</span></td>
            <td class="t14b">{{ $enrollment->course->level->name }}</td>
        </tr>

        <tr>
            <td style="text-align: center;" class="t12">Fecha de edicion:</td>
            <td class="t12">{{ \Carbon\Carbon::now(config('settings.courses_timezone'))->format('d/m/Y') }}</td>
        </tr>
    </table>

</div>

    <p class="t14b" style="text-align: center;">Periodo pedagogico: {{ $enrollment->course->period->name }}</p>
                    <table class="table" style="width: 18cm;">
                        @php $all_total = 0; @endphp
                        @php $all_grade = 0; @endphp
{{--                         <thead>
                            <tr>
                                <td style="font-weight: bold; font-size: larger">MODALIDAD</td>
                                <td>NOTA</td>
                            </tr>
                        </thead> --}}
                        <tbody>
                        @foreach ($grades->groupBy('grade_type_category') as $c => $category)
                        @php $cat_total = 0; @endphp
                        @php $cat_grade = 0; @endphp
                            <tr>
                                <td style="font-weight: bold; font-size: 10pt; text-decoration: underline;">{{ Str::upper($category[0]->grade_type_category) }}</td>
                                <td></td>
                                <td style="width: 5cm;"></td>
                            </tr>
                            @foreach ($category as $grade)
                                @php $cat_grade += $grade->grade; @endphp
                                @php $cat_total += $grade->gradeType->total; @endphp
                                <tr style="font-size: 11pt;">
                                    <td>{{ $grade->gradeType->name }}</td>
                                    <td>{{ $grade->grade }} / {{ $grade->gradeType->total }}</td>
                                    <td style="width: 5cm;"></td>
                                </tr>
                            @endforeach
                            <tr>
                                <td><strong>{{ __('Total') }}</strong></td>
                                {{-- specific to AF Loja to format legacy grade criterias correctly --}}
                                @if ($cat_total == 60 && $grade->gradeType->gradeTypeCategory_id == 2)
                                @php $cat_grade = $cat_grade / 2; @endphp
                                @php $cat_total = $cat_total / 2; @endphp
                                @endif
                                <td><strong>{{ $cat_grade }} / {{ $cat_total }}</strong></td>
                                <td style="width: 5cm;"></td>
                            </tr>

                            @php $all_grade += $cat_grade; @endphp
                            @php $all_total += $cat_total; @endphp
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                            @endforeach
                        </tbody>



                        <tr>
                            <th style="border: 2px solid black; border-collapse: collapse;"><strong>{{ __('Total') }}</strong></th>
                            <th style="border: 2px solid black; border-collapse: collapse;"><strong>{{ $all_grade }} / {{ $all_total }}</strong></th>
                            <th style="width: 5cm;">
                                <p>@foreach ($enrollment->result->comments as $comment) {{ $comment->body }} @endforeach</p>
                            </th>
                        </tr>

                        </table>

<p style="font-size: 11pt; text-align: center;">Nota minima para aprobar el módulo : 70/100</p>

<table style="margin-left: 0; width: 15cm;">
    <tr>
        <td style="text-align: left; width:5cm; font-size: 11pt; font-weight: bold;">
            RESULTADO
        </td>
        <td style="text-align: center; font-size: 11pt; font-weight: bold;">
            {{ $enrollment->result_name }}
            @if ($enrollment->result_name == "APROBADO")
                <br><i style="font-weight: normal;">Felicitaciones !</i>
            @endif
        </td>
    </tr>
</table>


<div style="margin-top: 1cm;">
    <span style="text-align: left; font-size: 10pt; font-weight: bold;">
        PROFESOR ENCARGADO DEL MÓDULO
    </span>
    <br>
    <span style="text-align: left; font-size: 11pt;">
        {{ $enrollment->course->teacher->name }}
    </span>
</div>

</div>
</body>
</html>
