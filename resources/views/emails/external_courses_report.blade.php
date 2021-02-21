<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif
        }
        
        .level {
            margin-left: 2em;
        }
    </style>
</head>
<body>
<h1>Rapport des cours externes</h1>
<h2>Période : {{ $period_start->locale('fr')->isoFormat('Do MMMM YYYY') }} - {{ $period_end->locale('fr')->isoFormat('Do MMM YYYY') }}</h2>
<h2>Institution : {{ $data['partner_name'] }}</h2>

@foreach ($data['courses'] as $course)
    <h4>Cours : {{ $course['course_name'] }}</h4>
    <div class="level">
        <p>Classes effectuées : {{ implode(' ; ', $course['events']) }}</p>
        <p>Soit un total de {{ $course['hours_count'] }} {{Str::plural('heure', $course['hours_count'])}} au prix horaire de ${{ $course['hourly_price'] }} = <strong>${{ $course['value'] }}</strong></p>
    </div>
@endforeach
<p style="color: red;">Total pour {{ $data['partner_name'] }} : <strong>${{ $data['partner_balance'] }}</strong></p>

</body>
</html>
