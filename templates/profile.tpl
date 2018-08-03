<center>
	<!--PROFILE PICTURE-->
	{if $profile->picture}
		<table cellpadding="3"><tr><td bgcolor="#202020">
		{img src="{$profile->picture_internal}" alt="Picture" width="300"}
		</td></tr></table>
	{else}
		{noimage width="300" height="200" text="Tristemente ...<br/>Sin foto de perfil :'-("}
	{/if}

	{space10}

	<!--ABOUT ME-->
	{$profile->about_me}
</center>

<!--TAGS-->
{if $profile->interests|@count gt 0}
	{space5}
	<table width="100%" cellpadding="3"><tr><td bgcolor="#F2F2F2">
		{space5}
		<center>
		<p>Cosas que me motivan:</p>
		<p>
		{foreach $profile->interests as $interest}
			{tag caption="{$interest}"}
		{/foreach}
		</p>
		</center>
		{space5}
	</td></tr></table>
{/if}

{space15}

<center>
{if $ownProfile}
	{if $profile->completion lt 85}
		<p><small>Solo ha llenado el <font color="red">{$profile->completion|number_format}%</font> de su perfil</small></p>
	{/if}
	{button href="PERFIL EDITAR" caption="Editar mi Perfil" body="Envie este email tal y como esta. Recibira como respuesta su perfil en modo de edicion."}

	{space15}

	<h1>Mi cr&eacute;dito</h1>
	<p>Usted tiene &sect;{$profile->credit|money_format} en cr&eacute;dito de Apretaste</p>

	{space10}

	<h1>Mis tickets para la rifa</h1>
	<p>Usted tiene {$tickets} ticket(s) para la {link href="RIFA" caption="rifa"}</p>
{else}
	{if $profile->blockedByMe}
		{button href="PERFIL DESBLOQUEAR @{$profile->username}" caption="Desbloquear" color="red" size="small"}
	{else}
		{button href="CHAT @{$profile->username}" caption="Chatear" color="grey" size="small"}
		{button href="PERFIL BLOQUEAR @{$profile->username}" caption="Bloquear" color="red" size="small"}
	{/if}
{/if}
</center>
