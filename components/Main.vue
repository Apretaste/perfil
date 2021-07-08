<template>
	<div class="mb-5">
		<profile-header :profile="profile"></profile-header>
		<profile-description :profile="profile"></profile-description>
		<gallery ref="gallery" v-model="profile.gallery" :can-edit="ownProfile"></gallery>
		<social-media :profile="profile" class="my-2"></social-media>
		<ap-fab :data="fabOptions"></ap-fab>
	</div>
</template>

<script>
module.exports = {
	components: {
		'ProfileHeader': httpVueLoader(apretaste.servicePath + 'components/ProfileHeader.vue'),
		'ProfileDescription': httpVueLoader(apretaste.servicePath + 'components/ProfileDescription.vue'),
		'Gallery': httpVueLoader(apretaste.servicePath + 'components/Gallery.vue'),
		'SocialMedia': httpVueLoader(apretaste.servicePath + 'components/SocialMedia.vue')
	},
	data: function () {
		const state = apretaste.request;

		state.fabOptions = [
			{icon: 'fas fa-share-alt', onTap: this.share},
			{icon: 'fas fa-arrow-left', onTap: apretaste.back},
			{icon: 'fas fa-hashtag', onTap: this.searchInPizarra},
		];

		if (state.ownProfile) {
			state.fabOptions.push({icon: 'fas fa-pen', onTap: this.openEdit});
		} else {
			// Other options
		}

		return state;
	},
	methods: {
		share: function () {
			apretaste.share({
				title: 'Mira el perfil de @' + this.profile.username + ' en Apretaste',
				link: 'http://apretaste.me/profile/' + this.profile.username
			});
		},
		openEdit: function () {
			apretaste.send({command: 'perfil editar'});
		},
		searchInPizarra: function () {
			apretaste.send({
				command: 'pizarra popular',
				data: {
					search: '@' + this.profile.username
				}
			});
		}
	}
}
</script>

<style scoped>

</style>