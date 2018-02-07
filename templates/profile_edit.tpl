<!-- PICTURE -->
{if {$APRETASTE_ENVIRONMENT} == "web"}
	<center>
		{if $person->picture}
			{img src="{$person->picture_internal}" alt="Picture" width="100" style="border:1px solid black;"}
		{else}{noimage}{/if}
		<br/>
		{button color="grey" href="PERFIL FOTO" desc="u:Adjunte su foto de perfil*" caption="Cambiar" size="small" wait="false" popup="true"}
	</center>
	{space15}
{/if}

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
		<td align="right" valign="middle">{select options="{$options->gender}" selected="{$person->gender}"}</td>
	</tr>

	<!-- SEXUAL ORIENTATION -->
	<tr>
		<td valign="middle"><small>Orientaci&oacute;n sexual</small></td>
		<td valign="middle"><b>{$person->sexual_orientation|lower|capitalize}</b></td>
		<td align="right" valign="middle">{select options="{$options->sexual_orientation}" selected="{$person->sexual_orientation}"}</td>
	</tr>

	<!-- DAY OF BIRTH -->
	<tr>
		<td valign="middle"><small>Cumplea&ntilde;os</small></td>
		<td valign="middle"><b>{$person->date_of_birth|date_format:"%e/%m/%Y"}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL CUMPLEANOS" desc="d:Escriba su fecha de cumpleannos usando la notacion DD/MM/AAAA, por ejemplo 5/2/1980" popup="true"  wait="false"}</td>
	</tr>

	<!-- BODY TYPE -->
	<tr>
		<td valign="middle"><small>Cuerpo</small></td>
		<td valign="middle"><b>{$person->body_type|lower|capitalize}</b></td>
		<td align="right" valign="middle">{select options="{$options->body_type}" selected="{$person->body_type}"}</td>
	</tr>

	<!-- EYES -->
	<tr>
		<td valign="middle"><small>Ojos</small></td>
		<td valign="middle"><b>{$person->eyes|lower|capitalize}</b></td>
		<td align="right" valign="middle">{select options="{$options->eyes}" selected="{$person->eyes}"}</td>
	</tr>

	<!-- HAIR -->
	<tr>
		<td valign="middle"><small>Pelo</small></td>
		<td valign="middle"><b>{$person->hair|lower|capitalize}</b></td>
		<td align="right" valign="middle">{select options="{$options->hair}" selected="{$person->hair}"}</td>
	</tr>

	<!-- SKIN -->
	<tr>
		<td valign="middle"><small>Piel</small></td>
		<td valign="middle"><b>{$person->skin|lower|capitalize}</b></td>
		<td align="right" valign="middle">{select options="{$options->skin}" selected="{$person->skin}"}</td>
	</tr>

	<!-- MARITAL STATUS -->
	<tr>
		<td valign="middle"><small>Estado civil</small></td>
		<td valign="middle"><b>{$person->marital_status|lower|capitalize}</b></td>
		<td align="right" valign="middle">{select options="{$options->marital_status}" selected="{$person->marital_status}"}</td>
	</tr>

	<!-- HIGHEST SCHOOL LEVEL-->
	<tr>
		<td valign="middle"><small>Nivel escolar</small></td>
		<td valign="middle"><b>{$person->highest_school_level|lower|capitalize}</b></td>
		<td align="right" valign="middle">{select options="{$options->highest_school_level}" selected="{$person->highest_school_level}"}</td>
	</tr>

	<!-- OCCUPATION -->
	<tr>
		<td valign="middle"><small>Profesi&oacute;n</small></td>
		<td valign="middle"><b>{$person->occupation|lower|capitalize}</b></td>
		<td align="right" valign="middle">{button size="small" color="grey" caption="Cambiar" href="PERFIL PROFESION" desc="Escriba su profesion. Por ejemplo profesor" popup="true" wait="false"}</td>
	</tr>

	<!-- COUNTRY -->
	<tr>
		<td valign="middle"><small>Pa&iacute;s</small></td>
		<td valign="middle">
			{if {$APRETASTE_ENVIRONMENT} eq "web"}
				<img class="flag" src="/images/flags/{$person->country|lower}.png" alt="{$person->country}"/>
			{/if}
			<b>{$person->country_name|lower|capitalize}</b>
		</td>
		<td align="right" valign="middle"><nobr>
			{select options="{$options->country_name}" selected="{$person->country_name}"}
			{button size="small" color="grey" caption="Otro" href="PERFIL PAIS" desc="Escriba el nombre del pais donde vive" popup="true" wait="false"}
		</nobr></td>
	</tr>

	<!-- PROVINCE-->
	{if $person->country == "CU"}
	<tr>
		<td valign="middle"><small>Provincia</small></td>
		<td valign="middle"><b>{$person->province|lower|capitalize}</b></td>
		<td align="right" valign="middle">{select options="{$options->province}" selected="{$person->province}"}</td>
	</tr>
	{/if}

	<!-- STATE-->
	{if $person->country == "US"}
	<tr>
		<td valign="middle"><small>Estado</small></td>
		<td valign="middle"><b>{$person->usstate_name|lower|capitalize}</b></td>
		<td align="right" valign="middle">{select options="{$options->usstate}" selected="{$person->usstate_name}"}</td>
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
		<td align="right" valign="middle">{select options="{$options->religion}" selected="{$person->religion}"}</td>
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
