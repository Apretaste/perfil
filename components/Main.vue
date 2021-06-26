<template>
	<div class="container-fluid">
		<profile-header :profile="profile"></profile-header>
		<profile-description :profile="profile"></profile-description>
		<gallery :images="profile.gallery"></gallery>
		<social-media :profile="profile"></social-media>
		<ap-fab :data="ui.fabOptions"></ap-fab>
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
		Vue.set(apretaste.state.ui, 'fabOptions', [
			{icon: 'fas fa-share-alt', onTap: this.share},
			{icon: 'fas fa-arrow-left', onTap: apretaste.back},
			{icon: 'fas fa-pen', onTap: this.openEdit},
			{icon: 'fas fa-hashtag', onTap: this.searchInPizarra},
		]);

		return apretaste.state;
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