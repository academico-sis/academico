<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CERTIFICADO DE APROBACIÓN DE MÓDULO</title>
    <style>
        .t11 {
            font-size: 11pt;
        }
        .t14 {
            font-size: 14pt;
        }

        .t18 {
            font-size: 18pt;
        }

        .t24 {
            font-size: 24pt;
            font-weight: bold;
        }

        .t28 {
            font-size: 28pt;
            font-weight: bold;
        }

        @page {
            background:url({{ storage_path('aflola/watemark.png') }}) no-repeat 0 0;
            background-image-resize: 3;
        }

        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="center">
        <img src="{{ storage_path('logo2.png') }}" alt="Logo" style="height: 3cm;">
    </div>

<div class="center" style="margin-top: 0.5cm;">
    <span class="t18">Otorga el siguiente</span><br>
    <span class="t18">CERTIFICADO DE APROBACIÓN DE MÓDULO</span><br>
    <span class="t24">- {{ Str::upper($enrollment->course->level->name) }} -</span><br>

    <span class="t14">Luego de haber cursado con éxito {{ $enrollment->course->volume}} horas de francés</span>

    <p class="t28">a: {{ Str::upper($enrollment->student->lastname) }} {{ $enrollment->student->firstname }}</p>

</div>
<div style="margin-top: 0.1cm; text-align: center;">
    <table style="margin: 0 auto 0 auto; width: 20cm;">
        <tr>
            <td>
                <p class="t14">En Loja, el {{ \Carbon\Carbon::now(config('settings.courses_timezone'))->isoFormat('D \d\e\ MMMM \d\e Y') }}</p>
            </td>
            <td class="center">
                <p style="font-family:frenchscript; font-size: 20pt;">¡Felicitaciones, siga adelante!</p>
                <div>
                    <img src="{{ storage_path('afloja/sig_camille.png') }}" style="width: 4cm; margin-bottom: 0.5cm; ">
                    <p class="t14">Sra. Camille Hannequart</p>
                    <p class="t11">Directora</p>
                </div>
            </td>
        </tr>
    </table>

</div>

</div>
</body>
</html>
