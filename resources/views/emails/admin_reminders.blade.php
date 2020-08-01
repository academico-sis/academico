<p>Bonjour,</p>
<p>Ceci est un message automatique, envoyé depuis la plateforme académique car la situation suivante a été détectée :</p>

@if ($changeNextPeriod)
<p>Le cycle d'inscriptions actuellement sélectionné sur la plateforme se termine dans moins de 15 jours. Les actions suivantes sont recommandées :</p>
<ul>
    <li>Sélectionner le prochain cycle comme cycle d'inscriptions par défaut</li>
    <li>Ne pas changer le cycle par défaut jusqu'à ce que toutes les classes du cycle en cours soient achevées.</li>
</ul>
<br>
@endif

@if ($changeCurrentPeriod)
<p>Le cycle par défaut actuellement sélectionné sur la plateforme est déjà terminé. Les actions suivantes sont recommandées :</p>
<ul>
    <li>Mettre à jour le cycle par défaut</li>
    <li>Mettre à jour le cycle d'inscriptions par défaut, si nécessaire.</li>
</ul>
<br>
@endif

<p>Ces actions peuvent être effectuées depuis la page {{ config('settings.app_url') }}/setup</p>
<p>En cas de doute, contactez votre administrateur.</p>