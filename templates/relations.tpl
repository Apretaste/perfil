<h1>Tus relaciones</h1>

<table border="0" width="100%">
	<tr><th align="center">Qui&eacute;n?</th>
	<th  align="center">Relaci&oacute;n</th><th  align="center">Desde</th></tr>
	{foreach item=item from=$relations}
		<tr>
			<td align="center">{link href ="PERFIL @{$item->who->username}" caption = "@{$item->who->username}"}</td>
			<td align="center">{$item->what}</td>
			<td align="center">{$item->since|date_format:"%d/%m/%Y"}</td>
		</tr>
	{/foreach}
</table>