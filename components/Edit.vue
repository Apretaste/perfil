<template>
	<div>
		<photo-edit ref="photoEdit" v-model="profile.picture" :is-influencer="profile.isInfluencer"></photo-edit>
		<form-edit :profile="profile" ref="form"></form-edit>
	</div>
</template>

<script>
module.exports = {
	components: {
		'PhotoEdit': httpVueLoader(apretaste.servicePath + 'components/PhotoEdit.vue'),
		'FormEdit': httpVueLoader(apretaste.servicePath + 'components/FormEdit.vue')
	},
	data: function () {
		return apretaste.request;
	}
}
</script>

<style scoped>

</style>