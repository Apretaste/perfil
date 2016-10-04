<h1>Selecciona el pa&iacute;s donde vives</h1>

<table>
{foreach name = countries item = $item from = $countries key = code}
	{if $smarty.foreach.countries.iteration is odd}
	<tr>
	{/if}
	<td>{link href="PERFIL PAIS {$item->code}" caption = "{$item->name}"}</td>
	{if $smarty.foreach.countries.iteration is even}
	</tr>
	{/if}
{/foreach}
</table>