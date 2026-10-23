<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            @if ($background)
                background: url("{{ $background }}") no-repeat 0 0;
                background-image-resize: 6;
            @endif
        }

        body {
            font-family: dejavuserif;
            color: #222222;
            text-align: center;
        }

        .institution {
            font-size: 14pt;
            margin-top: 4mm;
        }

        .title {
            font-size: 30pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 12mm 0 10mm 0;
        }

        .body {
            font-size: 16pt;
            line-height: 1.6;
            margin: 0 20mm;
        }

        .footer {
            width: 100%;
            margin-top: 18mm;
        }

        .footer td {
            width: 50%;
            vertical-align: bottom;
            font-size: 12pt;
        }

        .signer-function {
            font-size: 10pt;
            color: #555555;
        }
    </style>
</head>
<body>
    @if ($logo)
        <img src="{{ $logo }}" alt="" style="height: 25mm;">
    @endif

    <div class="institution">{{ $institution }}</div>

    <div class="title">{{ $title }}</div>

    <div class="body">{{ $body }}</div>

    <table class="footer">
        <tr>
            <td style="text-align: left;">{{ $place }}</td>
            <td style="text-align: center;">
                @if ($signature)
                    <img src="{{ $signature }}" alt="" style="height: 20mm;"><br>
                @endif
                @if ($signerName)
                    {{ $signerName }}<br>
                @endif
                @if ($signerFunction)
                    <span class="signer-function">{{ $signerFunction }}</span>
                @endif
            </td>
        </tr>
    </table>
</body>
</html>
