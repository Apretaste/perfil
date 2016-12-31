<center>
	{if ! empty($profile->thumbnail)}
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
			{tag caption="{$interest}"}
		{/foreach}
		</p>
		</center>
		{space5}
	</td></tr></table>
{/if}

{space10}

{if $ownProfile}
	<center>
		{assign var="btncaption" value="Editar mi Perfil"}
		{if $completion lt 85}
			<p><small>Solo ha llenado el <font color="red">{$completion|number_format}%</font> de su perfil</small></p>
			{assign var="btncaption" value="Completar mi Perfil"}
		{/if}
		{button href="PERFIL EDITAR" caption="{$btncaption}" body="Envie este email tal y como esta. Recibira como respuesta su perfil en modo de edicion."}
	</center>

	{space15}

	<h1>Mi cr&eacute;dito</h1>
	<p>Usted tiene ${$profile->credit|money_format} en cr&eacute;dito de Apretaste</p>
	
	<h1>Mis tickets para la rifa</h1>
	<p>Usted tiene {$profile->raffle_tickets} ticket(s) para la {link href="RIFA" caption="rifa"}</p>

	{space10}
{else}
	 <center>
		{button href="CUPIDO LIKE @{$profile->username}" caption="&hearts; Me gusta" color="green"} 
		{button href="NOTA @{$profile->username} Hola @{$profile->username}. Me gusto tu perfil. Pareces una persona interesante y me gustaria saber mas de ti. Por favor respondeme." caption="Enviar nota" color="grey" body="Cambie la nota en el asunto por la que usted desea"}
	</center>
{/if}
{if $notes}
	{space10}
	
	<h1>Mis &uacute;ltimas notas en Pizarra</h1>
	<table width="100%">
	{foreach from=$notes item=note}
		<tr {if $note@iteration is even}bgcolor="#F2F2F2"{/if}>
			<td>
				<big>{$note['text']|replace_url}</big>
				<br/>
				<small>
					{link href="PIZARRA LIKE {$note['id']}" caption="&hearts; Like" body="Envie este email tal como esta para expresar gusto por este post de este usuario"}
					[<font color="red">{$note['likes']}&hearts;</font>]
				</small>
				{space5}
			</td>
		</tr>
	{/foreach}
	</table>
	{if !$friend} 
	<center>{button color="blue" href="PIZARRA SEGUIR @{$profile->username}" caption="Seguir" body="Siga a @{$profile->username} y vea sus notas arriba en la pizarra"}</center>
	{/if}
	{space30}
{/if}
{if $sites}
	{space10}
	<h1>Mis sitios webs publicados en Apretaste!</h1>
		<ul>
		{foreach from=$sites item=site}
			<li>{link href="WEB http://{$site}.apretaste.com/index.html" caption ="{$site}.apretaste.com"}</li>
		{/foreach}
		</ul>
{/if}
<center>
	<p><small>&iquest;Extra&ntilde;as a tus amigos? {link href="INVITAR su@amigo.cu" caption="Inv&iacute;talos" body="Cambie en el asunto su@amigo.cu por el email de la persona a invitar. Puede agregar varios emails, separados por espacios o comas"} y gana tickets para {link href="RIFA" caption="nuestra rifa"}.</small></p>
</center>