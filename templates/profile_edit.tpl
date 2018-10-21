<!-- PICTURE -->
<center>
	{if $person->picture}
		{img src="{$person->picture_internal}" alt="Picture" width="100" style="border:1px solid black;"}
	{else}{noimage}{/if}
	<br>
	{*if count($person->extra_pictures)>0}
	{foreach $person->extra_pictures as $key => $picture}
		{link href="PERFIL VER {$picture}" caption="{img src="{$person->extraPictures_internal[$key]}" alt="Picture" width="45" style="border:.5px solid black;"}"}
	{/foreach}
	<br>
	{/if*}
	{button color="grey" href="PERFIL FOTO" desc="u:Adjunte su foto de perfil*" caption="Cambiar" size="small" popup="true"}
	{*if $person->picture}
	<br>
		{button color="grey" href="PERFIL EXTRAFOTO" desc="u:Adjunte su foto para su galeria*" caption="Subir foto" size="small" popup="true"}
	{/if*}
</center>
{space15}

<table id="profile" width="100%" cellspacing="0">
	<!-- NAME -->
	<tr>
		<td valign="middle"><small>Nombre</small></td>
		<td valign="middle"><b id="value_name">{$person->full_name|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL NOMBRE" desc="Escriba su nombre completo" popup="true" wait="false" callback="reloadName"}</td>
	</tr>

	<!-- GENDER -->
	<tr>
		<td valign="middle"><small>Sexo</small></td>
		<td valign="middle"><b id="value_sex">{$person->gender|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL SEXO" desc="m:Describa su genero [Masculino,Femenino]" popup="true" wait="false" callback="reloadSex"}</td>
	</tr>

	<!-- SEXUAL ORIENTATION -->
	<tr>
		<td valign="middle"><small>Orientaci&oacute;n sexual</small></td>
		<td valign="middle"><b id="value_orientation">{$person->sexual_orientation|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL ORIENTACION" desc="m:Describa su orientacion sexual [Hetero,Homo,Bi]" popup="true" wait="false" callback="reloadOrientation"}</td>
	</tr>

	<!-- DAY OF BIRTH -->
	<tr>
		<td valign="middle"><small>Cumplea&ntilde;os</small></td>
		<td valign="middle"><b id="value_birthday">{$person->date_of_birth|date_format:"%e/%m/%Y"}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL CUMPLEANOS" desc="d:Escriba su fecha de cumpleaños usando la notación DD/MM/AAAA, por ejemplo 5/2/1980" popup="true"  wait="false" callback="reloadBirthday"}</td>
	</tr>

	<!-- BODY TYPE -->
	<tr>
		<td valign="middle"><small>Cuerpo</small></td>
		<td valign="middle"><b id="value_body">{$person->body_type|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL CUERPO" desc="m:Describa su composicion fisica [Delgado,Medio,Extra,Atletico]" popup="true" wait="false" callback="reloadBody"}</td>
	</tr>

	<!-- EYES -->
	<tr>
		<td valign="middle"><small>Ojos</small></td>
		<td valign="middle"><b id="value_eyes">{$person->eyes|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL OJOS" desc="m:De que color son sus ojos? [Negro,Carmelita,Verde,Azul,Avellana,Otro]" popup="true" wait="false" callback="reloadEyes"}</td>
	</tr>

	<!-- HAIR -->
	<tr>
		<td valign="middle"><small>Pelo</small></td>
		<td valign="middle"><b id="value_hair">{$person->hair|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL PELO" desc="m:De que color es tu pelo? [Trigueno,Castano,Rubio,Negro,Rojo,Blanco,Otro]" popup="true"  wait="false" callback="reloadHair"}</td>
	</tr>

	<!-- SKIN -->
	<tr>
		<td valign="middle"><small>Piel</small></td>
		<td valign="middle"><b id="value_skin">{$person->skin|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL PIEL" desc="m:Describa su piel [Blanco,Negro,Mestizo,Otro]" popup="true"  wait="false" callback="reloadSkin"}</td>
	</tr>

	<!-- MARITAL STATUS -->
	<tr>
		<td valign="middle"><small>Estado civil</small></td>
		<td valign="middle"><b id="value_civilstatus">{$person->marital_status|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL ESTADO" desc="m:Describa su estado civil [Soltero,Saliendo,Comprometido,Casado]" popup="true"  wait="false" callback="reloadCivilStatus"}</td>
	</tr>

	<!-- HIGHEST SCHOOL LEVEL-->
	<tr>
		<td valign="middle"><small>Nivel escolar</small></td>
		<td valign="middle"><b id="value_school">{$person->highest_school_level|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL NIVEL" desc="m:Cual es su nivel escolar? [Primario,Secundario,Tecnico,Universitario,Postgraduado,Doctorado,Otro]" popup="true"  wait="false" callback="reloadSchool"}</td>
	</tr>

	<!-- OCCUPATION -->
	<tr>
		<td valign="middle"><small>Profesi&oacute;n</small></td>
		<td valign="middle"><b id="value_profesion">{$person->occupation|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL PROFESION" desc="m:Describa su profesion [Trabajador estatal,Cuentapropista,Estudiante,Ama de casa,Desempleado]" popup="true"  wait="false" callback="reloadProfesion"}</td>
	</tr>

	<!-- COUNTRY -->
	<tr>
		<td valign="middle"><small>Pa&iacute;s</small></td>
		<td valign="middle">
			<b id="value_country">
				{if {$APRETASTE_ENVIRONMENT} eq "web"}
					<img style="height:15px;" src="/images/flags/{$person->country|lower}.png" alt="{$person->country}"/>
				{/if}
				{$person->country_name|lower|capitalize}
			</b>
		</td>
		<td align="right" valign="middle"><nobr>
			{button size="small" color="grey" caption="Cambiar" href="PERFIL PAIS" desc="m:Escoja un pais de esta lista[Cuba,Estados Unidos,Espana,Italia,Mexico,Brasil,Ecuador,Canada,Venezuela,Alemania,Colombia]|t:O escriba el nombre del pais donde vive" popup="true" wait="false" callback="reloadCountry"}
		</nobr></td>
	</tr>

	<!-- PROVINCE-->
	<tr id="container_province" class="{if $person->country != 'CU'}hidden{/if}">
		<td valign="middle"><small>Provincia</small></td>
		<td valign="middle"><b id="value_province">{$person->province|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL PROVINCIA" desc="m:En que provincia vive? [Pinar_del_Rio,La_Habana,Artemisa,Mayabeque,Matanzas,Villa_Clara,Cienfuegos,Sancti_Spiritus,Ciego_de_Avila,Camaguey,Las_Tunas,Holguin,Granma,Santiago_de_Cuba,Guantanamo,Isla_de_la_Juventud]" popup="true"  wait="false" callback="reloadProvince"}</td>
	</tr>

	<!-- STATE-->
	<tr id="container_state" class="{if $person->country != 'US'}hidden{/if}">
		<td valign="middle"><small>Estado</small></td>
		<td valign="middle"><b id="value_state">{$person->usstate_name|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL USSTATE" desc="m:En que pais vives? [AL,AK,AZ,AR,CA,CO,CT,DE,FL,GA,HI,ID,IL,IN,IA,KS,KY,LA,ME,MD,MA,MI,MN,MS,MO,MT,NE,NV,NH,NJ,NM,NY,NC,ND,OH,OK,OR,PA,RI,SC,SD,TN,TX,UT,VT,VA,WA,WV,WI,WY]" popup="true"  wait="false" callback="reloadState"}</td>
	</tr>

	<!-- CITY -->
	<tr>
		<td valign="middle"><small>Ciudad</small></td>
		<td valign="middle"><b id="value_city">{$person->city|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL CIUDAD" desc="Escriba el nombre de la ciudad o pueblo donde vive" popup="true" wait="false" callback="reloadCity"}</td>
	</tr>

	<!-- INTERESTS -->
	<tr>
		<td valign="middle"><small>Intereses</small></td>
		<td valign="middle"><b id="value_interests">{$person->interests} intereses</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL INTERESES" desc="Escriba sus intereses separados por coma. Por ejemplo jardineria, musica, bailar" popup="true" wait="false" callback="reloadInterests"}</td>
	</tr>

	<!-- RELIGION -->
	<tr>
		<td valign="middle"><small>Religi&oacute;n</small></td>
		<td valign="middle"><b id="value_religion">{$person->religion|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL RELIGION" desc="m:Describa su religion [Cristianismo,Catolicismo,Yoruba,Protestante,Santero,Abakua,Budismo,Islam,Ateismo,Agnosticismo,Secularismo,Otra]" popup="true" wait="false" callback="reloadReligion"}</td>
	</tr>

	<!-- ORIGIN -->
	{if not $person->origin}
	<tr id="value_origin">
		<td valign="middle"><small>&iquest;Donde vi&oacute; la app?</small></td>
		<td valign="middle"></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL ORIGIN" desc="m:Donde escucho sobre la app? [{$origins}]" popup="true" wait="false" callback="reloadOrigin"}</td>
	</tr>
	{/if}
</table>

<style>
	#profile tr {
		height: 40px;
	}
	#profile tr:nth-child(odd) {
		background-color: #F2F2F2;
	}
	.hidden{
		display: none;
	}
</style>

<script>
	function reloadName(values) { document.getElementById('value_name').innerHTML = values[0]; }
	function reloadSex(values) { document.getElementById('value_sex').innerHTML = values[0]; }
	function reloadOrientation(values) { document.getElementById('value_orientation').innerHTML = values[0]; }
	function reloadBody(values) { document.getElementById('value_body').innerHTML = values[0]; }
	function reloadEyes(values) { document.getElementById('value_eyes').innerHTML = values[0]; }
	function reloadHair(values) { document.getElementById('value_hair').innerHTML = values[0]; }
	function reloadSkin(values) { document.getElementById('value_skin').innerHTML = values[0]; }
	function reloadCivilStatus(values) { document.getElementById('value_civilstatus').innerHTML = values[0]; }
	function reloadSchool(values) { document.getElementById('value_school').innerHTML = values[0]; }
	function reloadProfesion(values) { document.getElementById('value_profesion').innerHTML = values[0]; }
	function reloadProvince(values) { document.getElementById('value_province').innerHTML = values[0].replace("_", " "); }
	function reloadState(values) { document.getElementById('value_state').innerHTML = values[0]; }
	function reloadCity(values) { document.getElementById('value_city').innerHTML = values[0]; }
	function reloadInterests(values) { document.getElementById('value_interests').innerHTML = values[0].split(",").length + " intereses"; }
	function reloadReligion(values) { document.getElementById('value_religion').innerHTML = values[0]; }
	function reloadBirthday(values) {
		var dt = new Date(values[0]);
		var day = dt.getDate() + 1;
		var month = dt.getMonth() + 1;
		var year = dt.getFullYear();
		document.getElementById('value_birthday').innerHTML = day+"/"+month+"/"+year;
	}
	function reloadOrigin(values) {
		var origin = document.getElementById('value_origin');
		origin.parentNode.removeChild(origin);
	}
	function reloadCountry(values) {
		var country = values[0] == "" ? values[1] : values[0];
		document.getElementById('value_country').innerHTML = country;

		if(country == "Cuba") {
			document.getElementById('container_province').style.display = "table-row";
			document.getElementById('container_state').style.display = "none";
		}
		if(country == "Estados Unidos") {
			document.getElementById('container_state').style.display = "table-row";
			document.getElementById('container_province').style.display = "none";
		}
	}
</script>
