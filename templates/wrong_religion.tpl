<h1>Selecciona tu religi&oacute;n</h1>
<p>No reconocimos la religi&oacute;n que escribiste en el asunto del correo anterior. 
Te mostramos una lista de religiones a continuaci&oacute;n. Haz clic a la cual perteneces, y si no perteneces a ninguna haz clic en OTRA. 
Tambi&eacute;n incluimos opci&oacute;n para los ateos, agn&oacute;sticos y seculares.  </p>

<table width="100%"><tr>
{foreach item=item key=i from=$list}
<td valign="top">{link href="PERFIL RELIGION {$item}" caption ="{$item}"}</td>
{if ($i +1) %2 == 0}</tr><tr>{/if}
{/foreach}
</table>
<center>{button href="PERFIL RELIGION" caption="Quitar de mi perfil" color="red"}</center>