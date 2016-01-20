<h1>Selecciona tu provincia</h1>
<p>No reconocimos la provincia que escribiste en el asunto del correo anterior. 
Te mostramos la lista de las provincias a continuaci&oacute;n. Haz clic a la cual perteneces.</p>
<table width="100%"><tr>
{foreach item=item key=i from=$list}
<td valign="top">{link href="PERFIL PROVINCIA {$item}" caption ="{$item}"}</td>
{if ($i +1) %2 == 0}</tr><tr>{/if}
{/foreach}
</table>
<center>{button href="PERFIL PROVINCIA" caption="Quitar de mi perfil" color="red"}</center>