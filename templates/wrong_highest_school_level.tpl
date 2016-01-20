<h1>Selecciona tu nivel escolar</h1>
<p>No reconocimos tu nivel escolar que escribiste en el asunto del correo anterior. 
Haz clic en tu nivel escolar:</p>
<table width="100%"><tr>
{foreach item=item key=i from=$list}
<td valign="top">{link href="PERFIL NIVEL {$item}" caption ="{$item}"}</td>
{if ($i +1) %2 == 0}</tr><tr>{/if}
{/foreach}
</table>
<center>{button href="PERFIL NIVEL" caption="Quitar de mi perfil" color="red"}</center>