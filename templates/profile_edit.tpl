<!-- PICTURE -->
<center>
	{if $person->picture}
		{img src="{$person->picture_internal}" alt="Picture" width="100" style="border:1px solid black;"}
	{else}{noimage}{/if}
	<br>
	{if count($person->extra_pictures)>0}
	{foreach $person->extra_pictures as $key => $picture}
		{link href="PERFIL VER {$picture}" caption="
			{img src="{$person->extraPictures_internal[$key]}" alt="Picture" width="45" style="border:.5px solid black;"}
		"}
	{/foreach}
	<br>
	{/if}
	{button color="grey" href="PERFIL FOTO" desc="u:Adjunte su foto de perfil*" caption="Cambiar" size="small" wait="false" popup="true"}
	{if $person->picture}
	<br>
		{button color="grey" href="PERFIL EXTRAFOTO" desc="u:Adjunte su foto para su galeria*" caption="Subir foto" size="small" wait="false" popup="true"}
	{/if}
</center>
{space15}

<table id="profile" width="100%" cellspacing="0">
	<!-- NAME -->
	<tr>
		<td valign="middle"><small>Nombre</small></td>
		<td valign="middle"><b>{$person->full_name|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL NOMBRE" desc="Escriba su nombre completo" popup="true" wait="false"}</td>
	</tr>

	<!-- GENDER -->
	<tr>
		<td valign="middle"><small>Sexo</small></td>
		<td valign="middle"><b>{$person->gender|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL SEXO" desc="m:Describa su genero [Masculino,Femenino]" popup="true"  wait="false"}</td>
	</tr>

	<!-- SEXUAL ORIENTATION -->
	<tr>
		<td valign="middle"><small>Orientaci&oacute;n sexual</small></td>
		<td valign="middle"><b>{$person->sexual_orientation|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL ORIENTACION" desc="m:Describa su orientacion sexual [Hetero,Homo,Bi]" popup="true"  wait="false"}</td>
	</tr>

	<!-- DAY OF BIRTH -->
	<tr>
		<td valign="middle"><small>Cumplea&ntilde;os</small></td>
		<td valign="middle"><b>{$person->date_of_birth|date_format:"%e/%m/%Y"}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL CUMPLEANOS" desc="d:Escriba su fecha de cumpleaños usando la notación DD/MM/AAAA, por ejemplo 5/2/1980" popup="true"  wait="false"}</td>
	</tr>

	<!-- BODY TYPE -->
	<tr>
		<td valign="middle"><small>Cuerpo</small></td>
		<td valign="middle"><b>{$person->body_type|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL CUERPO" desc="m:Describa su composicion fisica [Delgado,Medio,Extra,Atletico]" popup="true"  wait="false"}</td>
	</tr>

	<!-- EYES -->
	<tr>
		<td valign="middle"><small>Ojos</small></td>
		<td valign="middle"><b>{$person->eyes|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL OJOS" desc="m:De que color son sus ojos? [Negro,Carmelita,Verde,Azul,Avellana,Otro]" popup="true"  wait="false"}</td>
	</tr>

	<!-- HAIR -->
	<tr>
		<td valign="middle"><small>Pelo</small></td>
		<td valign="middle"><b>{$person->hair|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL PELO" desc="m:De que color es tu pelo? [Trigueno,Castano,Rubio,Negro,Rojo,Blanco,Otro]" popup="true"  wait="false"}</td>
	</tr>

	<!-- SKIN -->
	<tr>
		<td valign="middle"><small>Piel</small></td>
		<td valign="middle"><b>{$person->skin|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL PIEL" desc="m:Describa su piel [Blanco,Negro,Mestizo,Otro]" popup="true"  wait="false"}</td>
	</tr>

	<!-- MARITAL STATUS -->
	<tr>
		<td valign="middle"><small>Estado civil</small></td>
		<td valign="middle"><b>{$person->marital_status|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL ESTADO" desc="m:Describa su estado civil [Soltero,Saliendo,Comprometido,Casado]" popup="true"  wait="false"}</td>
	</tr>

	<!-- HIGHEST SCHOOL LEVEL-->
	<tr>
		<td valign="middle"><small>Nivel escolar</small></td>
		<td valign="middle"><b>{$person->highest_school_level|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL NIVEL" desc="m:Cual es su nivel escolar? [Primario,Secundario,Tecnico,Universitario,Postgraduado,Doctorado,Otro]" popup="true"  wait="false"}</td>
	</tr>

	<!-- OCCUPATION -->
	<tr>
		<td valign="middle"><small>Profesi&oacute;n</small></td>
		<td valign="middle"><b>{$person->occupation|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL PROFESION" desc="m:Describa su profesion [Trabajador estatal,Cuentapropista,Estudiante,Ama de casa,Desempleado]" popup="true"  wait="false"}</td>
	</tr>

	<!-- COUNTRY -->
	<tr>
		<td valign="middle"><small>Pa&iacute;s</small></td>
		<td valign="middle">
			{if {$APRETASTE_ENVIRONMENT} eq "web"}
				<img style="height:15px;" src="/images/flags/{$person->country|lower}.png" alt="{$person->country}"/>
			{/if}
			<b>{$person->country_name|lower|capitalize}</b>
		</td>
		<td align="right" valign="middle"><nobr>
			{button size="small" color="grey" caption="Cambiar" href="PERFIL PAIS" desc="Escriba el nombre del pais donde vive" popup="true" wait="false"}
		</nobr></td>
	</tr>

	<!-- PROVINCE-->
	{if $person->country == "CU"}
	<tr>
		<td valign="middle"><small>Provincia</small></td>
		<td valign="middle"><b>{$person->province|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL PROVINCIA" desc="m:En que provincia vive? [Pinar_del_Rio,La_Habana,Artemisa,Mayabeque,Matanzas,Villa_Clara,Cienfuegos,Sancti_Spiritus,Ciego_de_Avila,Camaguey,Las_Tunas,Holguin,Granma,Santiago_de_Cuba,Guantanamo,Isla_de_la_Juventud]" popup="true"  wait="false"}</td>
	</tr>
	{/if}

	<!-- STATE-->
	{if $person->country == "US"}
	<tr>
		<td valign="middle"><small>Estado</small></td>
		<td valign="middle"><b>{$person->usstate_name|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL USSTATE" desc="m:En que pais vives? [AL,AK,AZ,AR,CA,CO,CT,DE,FL,GA,HI,ID,IL,IN,IA,KS,KY,LA,ME,MD,MA,MI,MN,MS,MO,MT,NE,NV,NH,NJ,NM,NY,NC,ND,OH,OK,OR,PA,RI,SC,SD,TN,TX,UT,VT,VA,WA,WV,WI,WY]" popup="true"  wait="false"}</td>
	</tr>
	{/if}

	<!-- CITY -->
	<tr>
		<td valign="middle"><small>Ciudad</small></td>
		<td valign="middle"><b>{$person->city|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL CIUDAD" desc="Escriba el nombre de la ciudad o pueblo donde vive" popup="true" wait="false"}</td>
	</tr>

	<!-- INTERESTS -->
	<tr>
		<td valign="middle"><small>Intereses</small></td>
		<td valign="middle"><b>{$person->interests} intereses</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL INTERESES" desc="Escriba sus intereses separados por coma. Por ejemplo jardineria, musica, bailar" popup="true" wait="false"}</td>
	</tr>

	<!-- RELIGION -->
	<tr>
		<td valign="middle"><small>Religi&oacute;n</small></td>
		<td valign="middle"><b>{$person->religion|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL RELIGION" desc="m:Describa su religion [Cristianismo,Catolicismo,Yoruba,Protestante,Santero,Abakua,Budismo,Islam,Ateismo,Agnosticismo,Secularismo,Otra]" popup="true"  wait="false"}</td>
	</tr>
</table>

<style>
	#profile tr {
		height: 40px;
	}
	#profile tr:nth-child(odd) {
		background-color: #F2F2F2;
	}
</style>
