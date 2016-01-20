<h1>Selecciona el color de tus ojos</h1>
<p>No reconocimos el color de tus ojos que escribiste en el asunto del correo anterior. 
Haz clic en el color de tus ojos.</p>
<table width="100%"><tr>
{foreach item=item key=i from=$list}
<td valign="top">{link href="PERFIL OJOS {$item}" caption ="{$item}"}</td>
{if ($i +1) %2 == 0}</tr><tr>{/if}
{/foreach}
</table>
<center>{button href="PERFIL OJOS" caption="Quitar de mi perfil" color="red"}</center>