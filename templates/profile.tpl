<center>
	{if ! empty($profile->picture)}
		<table cellpadding="3"><tr><td bgcolor="#202020">
		{img src="{$profile->thumbnail}" alt="Picture" width="300"}
		</td></tr></table>
	{else}
		{noimage width="300" height="200" text="Tristemente ...<br/>Sin foto de perfil :'-("}
	{/if}

	{space10}

	{if ! empty($message)}
		<p>{$message}</p>
	{/if}
</center>

{if $profile->about_me ne ""}
	<center>
		<p>{$profile->about_me}</p>
	</center>
{/if}

{if $profile->interests|@count gt 0}
	{space5}
	<table width="100%" cellpadding="3"><tr><td bgcolor="#F2F2F2">
		{space5}
		<center> 
		<p>Cosas que me motivan:</p>
		<p>
		{foreach $profile->interests as $interest}
		   	<nobr><span style="white-space:nowrap; padding:1px 8px; margin-top:5px; background-color:#202020; color:white; border-radius:5px; font-size:12px;">{$interest|upper}</span></nobr>
		{/foreach}
		</p>
		</center>
		{space5}
	</td></tr></table>
{/if}

{space10}

{if $ownProfile}
	<h1>Edite su perfil</h1>
	<p>Su perfil es una combinacion <b>PROPIEDAD = Valor</b>. Asigne un valor para cada PROPIEDAD despues del signo de igual (=) y envie el email. Adjunte una foto suya que aparecer&aacute; en su perfil.</p>

	{space10}

	<center>
		{button href="PERFIL EDITAR" caption="Editar mi Perfil" body="{$editProfileText}"}
		{button href="AYUDA PERFIL" caption="Ayuda" color="grey"}
	</center>

	{space15}

	<h1>Mi cr&eacute;dito</h1>
	<p>Usted tiene ${$profile->credit|money_format} en cr&eacute;dito de Apretaste</p>
	
	<h1>Mis tickets para la rifa</h1>
	<p>Usted tiene {$profile->raffle_tickets} ticket(s) para la {link href="RIFA" caption="rifa"}</p>
	
	{space10}
{/if}