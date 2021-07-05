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
			ref="description"
		></ap-area>
		<ap-input
			class="col-12"
			:data="{icon:'fas fa-user', label:'Nombre', value: profile.firstName}"
			ref="name"
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
			ref="sexualOrientation"
		></ap-combo>
		<ap-combo
			class="col-12"
			:data="maritalStatusCombo"
			ref="maritalStatus"
		></ap-combo>
	</div>
</template>

<script>
module.exports = {
	props: ['profile'],
	data: function () {
		return {
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
			}
		};
	},
	methods: {
		usernameChange: function (value) {
			this.profile.username = value;
		},
		nameChange: function (value) {
			this.profile.name = value;
		}
	}
}
</script>

<style scoped>

</style>