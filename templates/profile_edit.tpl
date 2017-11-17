<h1>Edite su perfil</h1>

{space5}

{if empty($person->picture) && ({$APRETASTE_ENVIRONMENT} != "app" && {$APRETASTE_ENVIRONMENT} != "web")}
<table width="100%">
	<tr>
		<td align="center" bgcolor="#F6CED8">
			<p><small>Usted no tiene foto de perfil. {link href="PERFIL FOTO" desc="Adjunte su foto de perfil" caption="Agregue su foto"} para aumentar en un 70% la posibilidad de que otros usuarios le escriban.</small></p>
		</td>
	</tr>
</table>
{/if}

<table width="100%">
	<!-- PICTURE -->
	{if $person->picture && ({$APRETASTE_ENVIRONMENT} != "app" && {$APRETASTE_ENVIRONMENT} != "web")}
	<tr>
		<td>Foto</td>
		<td valign="middle">
				{img src="{$person->picture_internal}" alt="Picture" width="100"}
			</td>
		<td align="right" valign="middle">
			{button href="PERFIL FOTO" desc="Adjunte su foto de perfil" caption="Cambiar" size="small"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>
	{/if}

	<!-- NAME -->
	<tr>
		<td valign="middle">Nombre</td>
		<td valign="middle"><b>{$person->full_name}</b></td>
		<td align="right" valign="middle">
			{if $person->full_name eq ""}{assign var="btncaption" value="Agregar"}{else}{assign var="btncaption" value="Cambiar"}{/if}
			{button size="small" caption="{$btncaption}" href="PERFIL NOMBRE" desc="Escriba su nombre completo" popup="true" wait="false"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>

	<!-- GENDER -->
	<tr>
		<td valign="middle">Sexo</td>
		<td valign="middle"><b>{$person->gender}</b></td>
		<td align="right" valign="middle">
			{link caption="Masculino" href="PERFIL SEXO MASCULINO" wait="false"}
			{separator}
			{link caption="Femenino" href="PERFIL SEXO FEMENINO" wait="false"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>

	<!-- SEXUAL ORIENTATION -->
	<tr>
		<td valign="middle">Orientaci&oacute;n sexual</td>
		<td valign="middle"><b>{$person->sexual_orientation}</b></td>
		<td align="right" valign="middle">
			{link caption="Hetero" href="PERFIL ORIENTACION HETERO" wait="false"}
			{separator}
			{link caption="Gay" href="PERFIL ORIENTACION HOMO" wait="false"}
			{separator}
			{link caption="Bi" href="PERFIL ORIENTACION BI" wait="false"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>

	<!-- DAY OF BIRTH -->
	<tr>
		<td valign="middle">Cumplea&ntilde;os</td>
		<td valign="middle"><b>{$person->date_of_birth|date_format:"%e/%m/%Y"}</b></td>
		<td align="right" valign="middle">
			{if $person->date_of_birth eq ""}{assign var="btncaption" value="Agregar"}{else}{assign var="btncaption" value="Cambiar"}{/if}
			{button size="small" caption="{$btncaption}" href="PERFIL CUMPLEANOS" desc="Escriba su fecha de cumpleannos usando la notacion DD/MM/AAAA, por ejemplo: 5/2/1980" popup="true"  wait="false"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>

	<!-- BODY TYPE -->
	<tr>
		<td valign="middle">Cuerpo</td>
		<td valign="middle"><b>{$person->body_type}</b></td>
		<td align="right" valign="middle">
			{link caption="Delgado" href="PERFIL CUERPO DELGADO" wait="false"}
			{separator}
			{link caption="Medio" href="PERFIL CUERPO MEDIO" wait="false"}
			{separator}
			{link caption="Extra" href="PERFIL CUERPO EXTRA" wait="false"}
			{separator}
			{link caption="Atl&eacute;tico" href="PERFIL CUERPO ATLETICO" wait="false"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>

	<!-- EYES -->
	<tr>
		<td valign="middle">Ojos</td>
		<td valign="middle"><b>{$person->eyes}</b></td>
		<td align="right" valign="middle">
			{link caption="Negros" href="PERFIL OJOS NEGRO" wait="false"}
			{separator}
			{link caption="Carmelitas" href="PERFIL OJOS CARMELITA" wait="false"}
			{separator}
			{link caption="Verdes" href="PERFIL OJOS VERDE" wait="false"}
			{separator}
			{link caption="Azules" href="PERFIL OJOS AZUL" wait="false"}
			{separator}
			{link caption="Avellana" href="PERFIL OJOS AVELLANA" wait="false"}
			{separator}
			{link caption="Otro color" href="PERFIL OJOS OTRO" wait="false"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>

	<!-- HAIR -->
	<tr>
		<td valign="middle">Pelo</td>
		<td valign="middle"><b>{$person->hair}</b></td>
		<td align="right" valign="middle">
			{link caption="Trigue&ntilde;o" href="PERFIL PELO TRIGUENO" wait="false"}
			{separator}
			{link caption="Casta&ntilde;o" href="PERFIL PELO CASTANO" wait="false"}
			{separator}
			{link caption="Rubio" href="PERFIL PELO RUBIO" wait="false"}
			{separator}
			{link caption="Negro" href="PERFIL PELO NEGRO" wait="false"}
			{separator}
			{link caption="Rojo" href="PERFIL PELO ROJO" wait="false"}
			{separator}
			{link caption="Blanco" href="PERFIL PELO BLANCO" wait="false"}
			{separator}
			{link caption="Otro" href="PERFIL PELO OTRO" wait="false"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>

	<!-- SKIN -->
	<tr>
		<td valign="middle">Piel</td>
		<td valign="middle"><b>{$person->skin}</b></td>
		<td align="right" valign="middle">
			{link caption="Blanca" href="PERFIL PIEL BLANCO" wait="false"}
			{separator}
			{link caption="Negra" href="PERFIL PIEL NEGRO" wait="false"}
			{separator}
			{link caption="Mestiza" href="PERFIL PIEL MESTIZO" wait="false"}
			{separator}
			{link caption="Otro" href="PERFIL PIEL OTRO" wait="false"}
		</td>
	</tr>

	<!-- MARITAL STATUS -->
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>
	<tr>
		<td valign="middle">Estado civil</td>
		<td valign="middle"><b>{$person->marital_status}</b></td>
		<td align="right" valign="middle">
			{link caption="Soltero" href="PERFIL ESTADO SOLTERO" wait="false"}
			{separator}
			{link caption="Saliendo" href="PERFIL ESTADO SALIENDO" wait="false"}
			{separator}
			{link caption="Comprometido" href="PERFIL ESTADO COMPROMETIDO" wait="false"}
			{separator}
			{link caption="Casado" href="PERFIL ESTADO CASADO" wait="false"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>

	<!-- HIGHEST SCHOOL LEVEL-->
	<tr>
		<td valign="middle">Nivel escolar</td>
		<td valign="middle"><b>{$person->highest_school_level}</b></td>
		<td align="right" valign="middle">
			{link caption="Primaria" href="PERFIL NIVEL PRIMARIO" wait="false"}
			{separator}
			{link caption="Secundaria" href="PERFIL NIVEL SECUNDARIO" wait="false"}
			{separator}
			{link caption="T&eacute;cnico" href="PERFIL NIVEl TECNICO" wait="false"}
			{separator}
			{link caption="Universitario" href="PERFIL NIVEl UNIVERSITARIO" wait="false"}
			{separator}
			{link caption="Postgraduado" href="PERFIL NIVEl POSTGRADUADO" wait="false"}
			{separator}
			{link caption="Doctorado" href="PERFIL NIVEl DOCTORADO" wait="false"}
			{separator}
			{link caption="Otro" href="PERFIL NIVEl OTRO" wait="false"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>

	<!-- OCCUPATION -->
	<tr>
		<td valign="middle">Profesi&oacute;n</td>
		<td valign="middle"><b>{$person->occupation}</b></td>
		<td align="right" valign="middle">
			{if $person->occupation eq ""}{assign var="btncaption" value="Agregar"}{else}{assign var="btncaption" value="Cambiar"}{/if}
			{button size="small" caption="{$btncaption}" href="PERFIL PROFESION" desc="Escriba su profesion. Por ejemplo: profesor" popup="true" wait="false"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>

	<!-- PROVINCE-->
	<tr>
		<td valign="middle">Provincia</td>
		<td valign="middle"><b>{$person->province}</b></td>
		<td align="right" valign="middle">
			{link caption="Pinar" href="PERFIL PROVINCIA PINAR_DEL_RIO" wait="false"}
			{separator}
			{link caption="Habana" href="PERFIL PROVINCIA LA_HABANA" wait="false"}
			{separator}
			{link caption="Artemisa" href="PERFIL PROVINCIA ARTEMISA" wait="false"}
			{separator}
			{link caption="Mayabeque" href="PERFIL PROVINCIA MAYABEQUE" wait="false"}
			{separator}
			{link caption="Matanzas" href="PERFIL PROVINCIA MATANZAS" wait="false"}
			{separator}
			{link caption="Las Villas" href="PERFIL PROVINCIA VILLA CLARA" wait="false"}
			{separator}
			{link caption="Cienfuegos" href="PERFIL PROVINCIA CIENFUEGOS" wait="false"}
			{separator}
			{link caption="Sancti Sp&iacute;ritus" href="PERFIL PROVINCIA SANCTI_SPIRITUS" wait="false"}
			{separator}
			{link caption="Ciego" href="PERFIL PROVINCIA CIEGO_DE_AVILA" wait="false"}
			{separator}
			{link caption="Camag&uuml;ey" href="PERFIL PROVINCIA CAMAGUEY" wait="false"}
			{separator}
			{link caption="Las Tunas" href="PERFIL PROVINCIA LAS_TUNAS" wait="false"}
			{separator}
			{link caption="Holgu&iacute;n" href="PERFIL PROVINCIA HOLGUIN" wait="false"}
			{separator}
			{link caption="Granma" href="PERFIL PROVINCIA GRANMA" wait="false"}
			{separator}
			{link caption="Santiago" href="PERFIL PROVINCIA SANTIAGO_DE_CUBA" wait="false"}
			{separator}
			{link caption="Guant&aacute;namo" href="PERFIL PROVINCIA GUANTANAMO" wait="false"}
			{separator}
			{link caption="Isla" href="PERFIL PROVINCIA ISLA_DE_LA_JUVENTUD" wait="false"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>

	<!-- CITY -->
	<tr>
		<td valign="middle">Ciudad</td>
		<td valign="middle"><b>{$person->city}</b></td>
		<td align="right" valign="middle">
			{if $person->city eq ""}{assign var="btncaption" value="Agregar"}{else}{assign var="btncaption" value="Cambiar"}{/if}
			{button size="small" caption="{$btncaption}" href="PERFIL CIUDAD" desc="Escriba el nombre de la ciudad o pueblo donde vive" popup="true" wait="false"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>

	<!-- COUNTRY -->
	<tr>
		<td valign="middle">Pa&iacute;s</td>
		<td valign="middle"><b>{$person->country_name}</b></td>
		<td align="right" valign="middle">
			{link href="PERFIL PAIS CU" caption="Cuba" wait="false"}
			{separator}
			{link href="PERFIL PAIS US" caption="EEUU" wait="false"}
			{separator}
			{button size="small" caption="Otro" href="PERFIL PAIS" desc="Escriba el nombre del pais donde vive" popup="true" wait="false"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>
	<!-- INTERESTS -->
	<tr>
		<td valign="middle">Intereses</td>
		<td valign="middle"><b>{$person->interests}</b></td>
		<td align="right" valign="middle">
			{if $person->interests eq ""}{assign var="btncaption" value="Agregar"}{else}{assign var="btncaption" value="Cambiar"}{/if}
			{button size="small" caption="{$btncaption}" href="PERFIL INTERESES" desc="Escriba sus intereses separados por coma. Por ejemplo: jardineria, musica, bailar" popup="true" wait="false"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>

	<!-- RELIGION -->
	<tr>
		<td valign="middle">Religi&oacute;n</td>
		<td valign="middle"><b>{$person->religion}</b></td>
		<td align="right" valign="middle">
			{link caption="Ate&iacute;smo" href="PERFIL RELIGION ATEISMO" wait="false"}
			{separator}
			{link caption="Secularismo" href="PERFIL RELIGION SECULARISMO" wait="false"}
			{separator}
			{link caption="Agnosticismo" href="PERFIL RELIGION AGNOSTICISMO" wait="false"}
			{separator}
			{link caption="Catolicismo" href="PERFIL RELIGION CATOLICISMO" wait="false"}
			{separator}
			{link caption="Cristianismo" href="PERFIL RELIGION CRISTIANISMO" wait="false"}
			{separator}
			{link caption="Islam" href="PERFIL RELIGION ISLAM" wait="false"}
			{separator}
			{link caption="Raftafarismo" href="PERFIL RELIGION PROTESTANTE" wait="false"}
			{separator}
			{link caption="Judaismo" href="PERFIL RELIGION JUDAISMO" wait="false"}
			{separator}
			{link caption="Espiritismo" href="PERFIL RELIGION SANTERO" wait="false"}
			{separator}
			{link caption="Sijismo" href="PERFIL RELIGION YORUBA" wait="false"}
			{separator}
			{link caption="Sijismo" href="PERFIL RELIGION ABAKUA" wait="false"}
			{separator}
			{link caption="Budismo" href="PERFIL RELIGION BUDISMO" wait="false"}
			{separator}
			{link caption="Otra" href="PERFIL RELIGION OTRA" wait="false"}
		</td>
	</tr>
	<tr>
		<td valign="middle" colspan="4"><hr/></td>
	</tr>
</table>

{space15}
<center>
	{button href="PERFIL" caption="Ver Perfil"}
</center>
{space10}
