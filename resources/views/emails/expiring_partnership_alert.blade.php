Le convenio avec {{ $partner->name }} expire le {{ Carbon\Carbon::parse($partner->expired_on)->locale('fr')->isoFormat('Do MMM YYYY') }}.

Ce convenio @if ($partner->auto_renewal) est @else n'est pas @endif renconduit tacitement.