<h1>Selecciona el color de tu pelo</h1>
<p>No reconocimos el color de pelo que escribiste en el asunto del correo anterior. 
Haz clic en el color de tu pelo:</p>
<table width="100%"><tr>
{foreach item=item key=i from=$list}
<td valign="top">{link href="PERFIL PELO {$item}" caption ="{$item}"}</td>
{if ($i +1) %2 == 0}</tr><tr>{/if}
{/foreach}
</table>
<center>{button href="PERFIL PELO" caption="Quitar de mi perfil" color="red"}</center>