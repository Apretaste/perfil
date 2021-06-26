<template>
	<div class="row">
		<ap-input
			class="col-12"
			:data="{icon:'fas fa-user', label:'@username', value: profile.username}"
			@change="usernameChange"
		></ap-input>
	</div>
</template>

<script>
module.exports = {
	props: ['profile'],
	methods: {
		usernameChange: function (event) {
			console.log(event);
		}
	}
}
</script>

<style scoped>

</style>