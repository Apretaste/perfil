<h1>Te invitamos a socializar</h1>
<p>No guardas ninguna relaci&oacute;n con ning&uacute;n otro usuario en Apretaste, pero no te desanimes. Ac&aacute; te ponemos algunos servicios que te ayudar&aacute;n a socializar.</p>

{if count($services) gt 0}
	{space10}
	<table border="0" width="100%">
		{foreach from=$services item=service}
		<tr>
			<td><b>{button color="grey" href="{$service->name}" caption="{$service->name}"}</b></td>
			<td>&nbsp;</td>
			<td>{$service->description}</td>
		</tr>
		{/foreach}
	</table>
{/if}
