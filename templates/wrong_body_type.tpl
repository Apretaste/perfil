<h1>Selecciona tu tipo de cuerpo</h1>
<p>No reconocimos el tipo de cuerpo que escribiste en el asunto del correo anterior. 
Haz clic en tu tipo de cuerpo:</p>
<table width="100%"><tr>
{foreach item=item key=i from=$list}
<td valign="top">{link href="PERFIL CUERPO {$item}" caption ="{$item}"}</td>
{if ($i +1) %2 == 0}</tr><tr>{/if}
{/foreach}
</table>
<center>{button href="PERFIL CUERPO" caption="Quitar de mi perfil" color="red"}</center>