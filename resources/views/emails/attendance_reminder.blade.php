<h3>Bonjour {{ $teacher->firstname }},</h3>

<p>Les fiches de présence suivantes sont incomplètes :</p>

<ul>
    @foreach ($events as $event)
        <li>{{ $event->name }} du {{ $event->start }}</li>
    @endforeach
</ul>