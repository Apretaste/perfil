<template>
	<div class="row">
		<ap-input
			class="col-12"
			:data="{icon:'fas fa-user', label:'@username', value: profile.username}"
			ref="username"
		></ap-input>
		<ap-area
			class="col-12"
			:data="{icon:'fas fa-user', label:'Acerca de', value: profile.aboutMe}"
			ref="about_me"
		></ap-area>
		<ap-input
			class="col-12"
			:data="{icon:'fas fa-user', label:'Nombre', value: profile.firstName}"
			ref="first_name"
		></ap-input>
		<ap-input
			class="col-12"
			:data="{icon:'fas fa-user', label:'Apellido', value: profile.lastName}"
			ref="last_name"
		></ap-input>
		<!--Birth date-->
		<ap-combo
			class="col-12"
			:data="genderCombo"
			ref="gender"
		></ap-combo>
		<ap-combo
			class="col-12"
			:data="sexualOrientationCombo"
			ref="sexual_orientation"
		></ap-combo>
		<ap-combo
			class="col-12"
			:data="maritalStatusCombo"
			ref="marital_status"
		></ap-combo>
		<ap-combo
			class="col-12"
			:data="educationCombo"
			ref="highest_school_level"
		></ap-combo>
		<ap-combo
			class="col-12"
			:data="occupationCombo"
			ref="occupation"
		></ap-combo>
		<ap-combo
			class="col-12"
			:data="religionCombo"
			ref="religion"
		></ap-combo>
		<ap-combo
			class="col-12"
			:data="locationCombo"
			ref="province"
		></ap-combo>
		<ap-input
			class="col-12"
			:data="{icon:'fas fa-phone', label:'Celular', value: profile.cellphone, type: 'tel'}"
			ref="cellphone"
		></ap-input>

		<div class="col-12">
			<ap-fab :data="[{icon: 'fa fa-save', onTap: this.submitProfileData}]"></ap-fab>
			<ap-toast v-if="error != null" :text="error"></ap-toast>
		</div>
	</div>
</template>

<script>
module.exports = {
	props: ['profile'],
	data: function () {
		return {
			error: null,
			genderCombo: {
				icon: 'fas fa-male',
				label: 'Género',
				selected: this.profile.gender,
				options: [
					{value: 'M', caption: 'Masculino'},
					{value: 'F', caption: 'Femenino'},
				]
			},
			sexualOrientationCombo: {
				label: 'Orientación sexual',
				icon: 'fa fa-heart',
				selected: this.profile.sexualOrientation,
				options: [
					{value: 'HETERO', caption: 'Heterosexual'},
					{value: 'HOMO', caption: 'Homosexual'},
					{value: 'BI', caption: 'Bisexual'}
				]
			},
			maritalStatusCombo: {
				label: 'Estado Civil',
				icon: 'fa fa-heart',
				selected: this.profile.maritalStatus,
				options: [
					{value: 'SOLTERO', caption: 'Soltero'},
					{value: 'SALIENDO', caption: 'Saliendo'},
					{value: 'COMPROMETIDO', caption: 'Comprometido'},
					{value: 'CASADO', caption: 'Casado'},
					{value: 'DIVORCIADO', caption: 'Divorciado'},
					{value: 'VIUDO', caption: 'Viudo'}
				]
			},
			educationCombo: {
				label: 'Educación',
				icon: 'fa fa-graduation-cap',
				selected: this.profile.education,
				options: [
					{value: 'PRIMARIO', caption: 'Primario'},
					{value: 'SECUNDARIO', caption: 'Secundario'},
					{value: 'TECNICO', caption: 'Técnico'},
					{value: 'UNIVERSITARIO', caption: 'Universitario'},
					{value: 'POSTGRADUADO', caption: 'Postgraduado'},
					{value: 'DOCTORADO', caption: 'Doctorado'},
					{value: 'OTRO', caption: 'Otro'}
				]
			},
			occupationCombo: {
				label: 'Ocupación',
				icon: 'fa fa-business-time',
				selected: this.profile.occupation,
				options: [
					{value: 'AMA_DE_CASA', caption: 'Ama de casa'},
					{value: 'ESTUDIANTE', caption: 'Estudiante'},
					{value: 'EMPLEADO_PRIVADO', caption: 'Empleado Privado'},
					{value: 'EMPLEADO_ESTATAL', caption: 'Empleado Estatal'},
					{value: 'INDEPENDIENTE', caption: 'Trabajador Independiente'},
					{value: 'JUBILADO', caption: 'Jubilado'},
					{value: 'DESEMPLEADO', caption: 'Desempleado'}
				]
			},
			religionCombo: {
				label: 'Religión',
				icon: 'fa fa-place-of-worship',
				selected: this.profile.religion,
				options: [
					{value: 'ATEISMO', caption: 'Ateísmo'},
					{value: 'SECULARISMO', caption: 'Secularismo'},
					{value: 'AGNOSTICISMO', caption: 'Agnosticismo'},
					{value: 'ISLAM', caption: 'Islamismo'},
					{value: 'JUDAISTA', caption: 'Judaísmo'},
					{value: 'ABAKUA', caption: 'Abakuá'},
					{value: 'SANTERO', caption: 'Santería'},
					{value: 'YORUBA', caption: 'Yorubismo'},
					{value: 'BUDISMO', caption: 'Budismo'},
					{value: 'CATOLICISMO', caption: 'Catolicismo'},
					{value: 'CRISTIANISMO', caption: 'Cristianismo'},
					{value: 'PROTESTANTE', caption: 'Protestante'},
					{value: 'OTRA', caption: 'Otra'}
				]
			},
			locationCombo: {
				label: 'Localización',
				icon: 'fa fa-map-marker-alt',
				selected: this.profile.province,
				options: [
					{value: 'PINAR_DEL_RIO', caption: 'Pinar del Río'},
					{value: 'LA_HABANA', caption: 'La Habana'},
					{value: 'ARTEMISA', caption: 'Artemisa'},
					{value: 'MAYABEQUE', caption: 'Mayabeque'},
					{value: 'MATANZAS', caption: 'Matanzas'},
					{value: 'VILLA_CLARA', caption: 'Villa Clara'},
					{value: 'CIENFUEGOS', caption: 'Cienfuegos'},
					{value: 'SANCTI_SPIRITUS', caption: 'Sancti Spiritus'},
					{value: 'CIEGO_DE_AVILA', caption: 'Ciego de Ávila'},
					{value: 'CAMAGUEY', caption: 'Camagüey'},
					{value: 'LAS_TUNAS', caption: 'Las Tunas'},
					{value: 'HOLGUIN', caption: 'Holguín'},
					{value: 'GRANMA', caption: 'Granma'},
					{value: 'SANTIAGO_DE_CUBA', caption: 'Santiago de Cuba'},
					{value: 'GUANTANAMO', caption: 'Guantánamo'},
					{value: 'ISLA_DE_LA_JUVENTUD', caption: 'Isla de la Juventud'},
					{value: 'OUT_OF_CUBA', caption: 'Fuera de Cuba'}
				]
			}
		};
	},
	watch: {
		error: function (val) {
			const thisElement = this;
			if (val != null) {
				setTimeout(function () {
					thisElement.error = null;
				}, 5000);
			}
		}
	},
	methods: {
		submitProfileData: function () {
			// array of possible values
			const props = ['first_name', 'last_name', 'about_me', 'year_of_birth', 'month_of_birth', 'day_of_birth', 'province', 'gender', 'sexual_orientation', 'marital_status', 'religion', 'highest_school_level', 'occupation', 'cellphone'];

			// create object to send to the backend
			const data = {};
			const thisElement = this;
			props.forEach(function (prop) {
				const ref = thisElement.$refs[prop];
				if (!ref) {
					return;
				}

				if (
					ref.value() !== thisElement.profile[prop] &&
					ref.value() != null &&
					ref.value() !== ""
				) {
					data[prop] = ref.value();
					thisElement.profile[prop] = ref.value();
				}
			});

			// add username to data object
			const cleanUsername = this.$refs['username'].value().replace('@', '');

			if (cleanUsername.substr(-3).toLowerCase() === 'bot') {
				this.error = 'El nombre de usuario no debe terminar en la palabra "bot"';
				return false;
			}

			if (cleanUsername !== this.profile.username && cleanUsername !== '') {
				data.username = cleanUsername;
			}

			if (!isNaN(cleanUsername)) {
				this.error = 'El nombre de usuario debe contener al menos una letra';
				return;
			}

			// do not send empty petitions
			if (data) {
				this.error = "Usted no ha hecho ningún cambio";
				return;
			}

			// save changes in the database
			apretaste.send({
				command: "PERFIL UPDATE",
				data: data,
				redirect: false,
				callback: {
					name: 'vm.$refs.main.$refs.form.onSaveCallback'
				}
			});

			// show success alert
			this.error = "Sus cambios han sido guardados";
		},
		onSaveCallback: function () {
			apretaste.send({'command': 'PERFIL', useCache: false});
		}
	}
}
</script>

<style scoped>

</style>