<h1>&iquest;Donde escucho sobre la app?</h1>

<p>Gracias por dejarnos saber donde escucho sobre la app para seguir acercando nuestro servicios a m&aacute;s personas.</p>

{if $person->origin}
	<p>Usted nos dijo que vio la app en: <b>{$person->origin}</b></p>
{/if}

<center>
	{button caption="Dejarnos saber" href="PERFIL ORIGIN" desc="m:Donde escucho sobre la app? [{$origins}]" popup="true"  wait="false"}</td>
</center>
