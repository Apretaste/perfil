<h1>Edite su perfil</h1>
<center>
{if ! empty($thumbnail)}
	<table cellpadding="3"><tr><td valign="top" bgcolor="#202020">
	{img src="{$thumbnail}" alt="Picture" width="300"}
	</td></tr></table>
	{button href="PERFIL FOTO" body="Adjunte su foto de perfil" caption="Cambiar" size="small"}
	{button href="PERFIL FOTO" caption="Quitar" size="small"}<br/>
{else}
	{noimage width="300" height="200" text="Tristemente ...<br/>Sin foto de perfil :'-("}
	{button href="PERFIL FOTO" body="Adjunte su foto de perfil" caption="Agregar" size="small"}	
{/if}
</center>
<hr/>
{space5}
<table width="100%">
	<!-- NAME -->
	<tr>
		<td valign="top">Nombre</td>
		<td valign="top"><b>{$full_name}</b></td>
		<td valign="top">{button size="small" color="green" caption="Cambiar" href="PERFIL NOMBRE {$full_name}"}</td> 
		<td valign="top" width="100" align="right">{button size="small" color="red" caption="Quitar" href="PERFIL NOMBRE"}</td>
	</tr>
	<tr><td valign="top" colspan="4"><hr/></td></tr>
	
	<!-- GENDER -->
	<tr>
		<td valign="top">Sexo</td>
		<td valign="top"><b>{$gender}</b></td>
		<td valign="top">
		{link caption="Masculino" href="PERFIL SEXO MASCULINO"}
		{link caption="Femenino" href="PERFIL SEXO FEMENINO"}
		</td>
		<td valign="top" width="100" align="right">{button size="small" color="red" caption="Quitar" href="PERFIL SEXO"}</td>
	</tr>
	<tr><td valign="top" colspan="4"><hr/></td></tr>
	
	<!-- SEXUAL ORIENTATION -->
	<tr>
		<td valign="top">Orientaci&oacute;n sexual</td>
		<td valign="top"><b>{$sexual_orientation}</b></td>
		<td valign="top">
			{link caption="Hetero" href="PERFIL ORIENTACION HETERO"} <small>|</small>
			{link caption="Gay" href="PERFIL ORIENTACION HOMO"} <small>|</small>
			{link caption="Bi" href="PERFIL ORIENTACION BI"}</td>
		<td valign="top" width="100" align="right">{button size="small" color="red" caption="Quitar" href="PERFIL ORIENTACION"}</td>
	</tr>
	<tr><td valign="top" colspan="4"><hr/></td></tr>
	
	<!-- DAY OF BIRTH -->
	<tr>
		<td valign="top">Cumplea&ntilde;os</td>
		<td valign="top"><b>{$date_of_birth}</b></td>
		<td valign="top">{button size="small" color="green" caption="Cambiar" href="PERFIL CUMPLEANOS {$date_of_birth}"}</td>
		<td valign="top" width="100" align="right">{button size="small" color="red" caption="Quitar" href="PERFIL CUMPLEANOS"}</td>
	</tr>
	<tr><td valign="top" colspan="4"><hr/></td></tr>
	
	<!-- BODY TYPE -->
	<tr>
		<td valign="top">Cuerpo</td>
		<td valign="top"><b>{$body_type}</b></td>
		<td valign="top">
		{link caption="Delgado" href="PERFIL CUERPO DELGADO"}
		{link caption="Medio" href="PERFIL CUERPO MEDIO"}
		{link caption="Extra" href="PERFIL CUERPO EXTRA"}
		{link caption="Cambiar" href="PERFIL CUERPO ATLETICO"}
		</td>
		<td valign="top" width="100" align="right">{button size="small" color="red" caption="Quitar" href="PERFIL CUERPO"}</td>
	</tr>
	<tr><td valign="top" colspan="4"><hr/></td></tr>
	
	<!-- EYES -->
	<tr>	
		<td valign="top">Ojos</td>
		<td valign="top"><b>{$eyes}</b></td>
		<td valign="top">
			{link caption="Negros" href="PERFIL OJOS NEGRO"}
			{link caption="Carmelitas" href="PERFIL OJOS CARMELITA"}
			{link caption="Verdes" href="PERFIL OJOS VERDE"}
			{link caption="Azules" href="PERFIL OJOS AZUL"}
			{link caption="Avellana" href="PERFIL OJOS AVELLANA"}
			{link caption="Otro color" href="PERFIL OJOS OTRO"}</td>	
		<td valign="top" width="100" align="right">{button size="small" color="red" caption="Quitar" href="PERFIL OJOS"}</td>
	</tr>
	<tr><td valign="top" colspan="4"><hr/></td></tr>
	
	<!-- HAIR -->
	<tr>
		<td valign="top">Pelo</td>
		<td valign="top"><b>{$hair}</b></td>
		<td valign="top">{link caption="Trigue&ntilde;o" href="PERFIL PELO TRIGUENO"}
			{link caption="Casta&ntilde;o" href="PERFIL PELO CASTANO"}
			{link caption="Rubio" href="PERFIL PELO RUBIO"}
			{link caption="Negro" href="PERFIL PELO NEGRO"}
			{link caption="Rojo" href="PERFIL PELO ROJO"}
			{link caption="Blanco" href="PERFIL PELO BLANCO"}
			{link caption="Otro" href="PERFIL PELO OTRO"}</td>
		<td valign="top" width="100" align="right">{button size="small" color="red" caption="Quitar" href="PERFIL PELO"}</td>
	</tr>
	<tr><td valign="top" colspan="4"><hr/></td></tr>
	
	<!-- SKIN -->
	<tr>
		<td valign="top">Color de piel</td>
		<td valign="top"><b>{$skin}</b></td>
		<td valign="top">
		{link caption="Blanca" href="PERFIL PIEL BLANCO"}
		{link caption="Negra" href="PERFIL PIEL NEGRO"}
		{link caption="Mestiza" href="PERFIL PIEL MESTIZO"}
		{link caption="Otro" href="PERFIL PIEL OTRO"}</td>
		<td valign="top" width="100" align="right">{button size="small" color="red" caption="Quitar" href="PERFIL PIEL"}</td>
	</tr>
	
	<!-- MARITAL STATUS -->
	<tr><td valign="top" colspan="4"><hr/></td></tr>
	<tr>
		<td valign="top">Estado civil</td>
		<td valign="top"><b>{$marital_status}</b></td>
		<td valign="top">{link caption="Soltero" href="PERFIL ESTADO SOLTERO"}
			{link caption="Saliendo" href="PERFIL ESTADO SALIENDO"}
			{link caption="Comprometido" href="PERFIL ESTADO COMPROMETIDO"}
			{link caption="Casado" href="PERFIL ESTADO CASADO"}</td>
		<td valign="top" width="100" align="right">{button size="small" color="red" caption="Quitar" href="PERFIL ESTADO"}</td>
	</tr>
	<tr><td valign="top" colspan="4"><hr/></td></tr>
	
	<!-- HIGHEST SCHOOL LEVEL-->
	<tr>
		<td valign="top">Nivel escolar</td>
		<td valign="top"><b>{$highest_school_level}</b></td>
		<td valign="top">{link caption="Primaria" href="PERFIL NIVEL PRIMARIO"}
			{link caption="Secundaria" href="PERFIL NIVEL SECUNDARIO"}
			{link caption="T&eacute;nico" href="PERFIL NIVEl TECNICO"}
			{link caption="Universitario" href="PERFIL NIVEl UNIVERSITARIO"}
			{link caption="Postgraduado" href="PERFIL NIVEl POSTGRADUADO"}
			{link caption="Doctorado" href="PERFIL NIVEl DOCTORADO"}
			{link caption="Otro" href="PERFIL NIVEl OTRO"}</td>
		<td valign="top" width="100" align="right">{button size="small" color="red" caption="Quitar" href="PERFIL NIVEL"}</td>
	</tr>
	<tr><td valign="top" colspan="4"><hr/></td></tr>
	
	<!-- OCCUPATION -->
	<tr>
		<td valign="top">Profesi&oacute;n</td>
		<td valign="top"><b>{$occupation}</b></td>
		<td valign="top">{button size="small" color="green" caption="Cambiar" href="PERFIL PROFESION {$occupation}"}</td>
		<td valign="top" width="100" align="right">{button size="small" color="red" caption="Quitar" href="PERFIL PROFESION"}</td>
	</tr>
	<tr><td valign="top" colspan="4"><hr/></td></tr>
	
	<!-- PROVINCE-->
	<tr>
		<td valign="top">Provincia</td>
		<td valign="top"><b>{$province}</b></td>
		<td valign="top">
			{link caption="Pinar del R&iacute;o" href="PERFIL PINAR_DEL_RIO"}&nbsp;&nbsp;
			{link caption="La Habana" href="PERFIL PROVINCIA LA_HABANA"}&nbsp;&nbsp;
			{link caption="Artemisa" href="PERFIL PROVINCIA ARTEMISA"}&nbsp;&nbsp;
			{link caption="Mayabeque" href="PERFIL PROVINCIA MAYABEQUE"}&nbsp;&nbsp;
			{link caption="Matanzas" href="PERFIL PROVINCIA MATANZAS"}&nbsp;&nbsp; 
			{link caption="Villa Clara" href="PERFIL PROVINCIA VILLA CLARA"}&nbsp;&nbsp; 
			{link caption="Cienfuegos" href="PERFIL PROVINCIA CIENFUEGOS"}&nbsp;&nbsp;  
			{link caption="Sancti Sp&iacute;ritus" href="PERFIL PROVINCIA SANCTI_SPIRITUS"}&nbsp;&nbsp; 
			{link caption="Ciego de &Aacute;vila" href="PERFIL PROVINCIA CIEGO_DE_AVILA"}&nbsp;&nbsp;
			{link caption="Camag&uuml;ey" href="PERFIL PROVINCIA CAMAGUEY"}&nbsp;&nbsp;
			{link caption="Las Tunas" href="PERFIL PROVINCIA LAS_TUNAS"}&nbsp;&nbsp;
			{link caption="Holgu&iacute;n" href="PERFIL PROVINCIA HOLGUIN"}&nbsp;&nbsp; 
			{link caption="Granma" href="PERFIL PROVINCIA GRANMA"}&nbsp;&nbsp;
			{link caption="Santiago de Cuba" href="PERFIL SANTIAGO_DE_CUBA"}&nbsp;&nbsp;
			{link caption="Guant&aacute;namo" href="PERFIL GUANTANAMO"}&nbsp;&nbsp;<br/> 
			{link caption="Isla de la Juventud" href="PERFIL ISLA_DE_LA_JUVENTUD"}
		</td>
		<td valign="top" width="100" align="right">{button size="small" color="red" caption="Quitar" href="PERFIL PROVINCIA"}</td>
	</tr>
	<tr><td valign="top" colspan="4"><hr/></td></tr>
	
	<!-- CITY -->
	<tr>
		<td valign="top">Ciudad</td>
		<td valign="top"><b>{$city}</b></td>
		<td valign="top">{button size="small" color="green" caption="Cambiar" href="PERFIL CIUDAD {$city}"}</td>
		<td valign="top" width="100" align="right">{button size="small" color="red" caption="Quitar" href="PERFIL CIUDAD"}</td>
	</tr>
	<tr><td valign="top" colspan="4"><hr/></td></tr>
	
	<!-- INTERESTS -->
	<tr>
		<td valign="top">Intereses</td>
		<td valign="top"><b>{$interests}</b></td>
		<td valign="top">{button size="small" color="green" caption="Cambiar" href="PERFIL INTERESES {$interests}"}</td>
		<td valign="top" width="100" align="right">{button size="small" color="red" caption="Quitar" href="PERFIL INTERESES"}</td>
	</tr>
	<tr><td valign="top" colspan="4"><hr/></td></tr>
	
	<!-- RELIGION -->
	<tr>
		<td valign="top">Religi&oacute;n</td>
		<td valign="top"><b>{$religion}</b></td>
		<td valign="top">{button size="small" color="green" caption="Cambiar" href="PERFIL RELIGION {$religion}"}</td>
		<td valign="top" width="100" align="right">{button size="small" color="red" caption="Quitar" href="PERFIL RELIGION"}</td>
	</tr>
	<tr><td valign="top" colspan="4"><hr/></td></tr>
</table>