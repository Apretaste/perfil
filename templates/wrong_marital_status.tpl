<h1>Selecciona tu estado civil</h1>
<p>No reconocimos tu estado civil que escribiste en el asunto del correo anterior. 
Haz clic en tu estado civil:</p>
<table width="100%"><tr>
{foreach item=item key=i from=$list}
<td valign="top">{link href="PERFIL ESTADO {$item}" caption ="{$item}"}</td>
{if ($i +1) %2 == 0}</tr><tr>{/if}
{/foreach}
</table>
{space10}
<center>{button href="PERFIL ESTADO" caption="Quitar de mi perfil" color="red"}</center>