{* Smarty *}
<html>
<head>
    <title>Coupon</title>
</head>
<body>
<h1>Coupon formazione</h1>

<p>Spett.le {$ausind.receiver_name} {$ausind.receiver_surname},</p>
<p>
    {$ausind.associazione_name} ha provveduto a fornirle un coupon per usufruire di un corso di e-learning.
</p>
<p>
    Per usufruirne deve prima registrarsi sul portale <a href="{$ausind.associazione_url}">{$ausind.associazione_name}</a>:
    <br />
    Le credenziali così create saranno valide anche per gli eventuali futuri corsi e-learning.
    <br />
    Una volta registrato e loggato sul portale, inserisca il coupon seguente nel form che troverà sotto la voce "coupon" del menu principale.
</p>
Ecco il {$ausind.coupon_number} coupon a Lei riservato.
</p>

<h3>{$coursename}</h3>

<div style="font-family: monospace; font-size: 24px;">
        <b>{$coupon}</b>
</div>

<p>
    <b>Per una migliore fruizione del corso consigliamo fortemente di usare browser quali Firefox (versione 4 o superiore), Google Chrome (versione 6 o superiore), Explorer (dalla versione 9)</b>
<p>

<p>
    Cordiali saluti<br />
    Lo staff {$ausind.associazione_name}
</p>
<p>Questa mail è generata automaticamente, si prega di non rispondere.</p>

</body>
</html>
