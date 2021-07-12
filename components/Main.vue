<template>
	<div class="mb-5">
		<profile-header :profile="profile"></profile-header>
		<profile-description :profile="profile"></profile-description>
		<gallery ref="gallery" v-model="profile.gallery" :can-edit="ownProfile"></gallery>
		<social-media :profile="profile" class="my-2"></social-media>
		<ap-fab :data="fabOptions"></ap-fab>

		<ap-drawer ref="drawer"></ap-drawer>
		<ap-toast ref="toast"></ap-toast>
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
		return apretaste.request;
	},
	computed: {
		fabOptions: function () {
			const fabOptions = [
				{icon: 'fas fa-share-alt', onTap: this.share},
				{icon: 'fas fa-arrow-left', onTap: apretaste.back},
				{icon: 'fas fa-thumbs-up', onTap: this.searchInPizarra},
			];

			if (this.ownProfile) {
				fabOptions.push({icon: 'fas fa-image', onTap: this.addImage});
				fabOptions.push({icon: 'fas fa-pen', onTap: this.openEdit});
			} else if (this.type === 'friends') {
				fabOptions.push({icon: 'fa fa-ban', onTap: this.openBlockDrawer});
				fabOptions.push({icon: 'fa fa-times', onTap: this.openDeleteDrawer});
				fabOptions.push({icon: 'fa fa-comments', onTap: this.openChat});
			} else if (this.type === 'waitingForMe') {
				fabOptions.push({icon: 'fa fa-times', onTap: this.openRejectDrawer});
				fabOptions.push({icon: 'fa fa-user-plus', onTap: this.openAcceptDrawer});
			} else if (this.type === 'waiting') {
				fabOptions.push({icon: 'fa fa-times', onTap: this.openCancelDrawer});
				fabOptions.push({icon: 'fa fa-ban', onTap: this.openBlockDrawer});
			} else {
				fabOptions.push({icon: 'fa fa-ban', onTap: this.openBlockDrawer});
				fabOptions.push({icon: 'fa fa-user-plus', onTap: this.openAddDrawer});
			}

			return fabOptions;
		}
	},
	methods: {
		share: function () {
			apretaste.share({
				title: 'Mira el perfil de @' + this.profile.username + ' en Apretaste',
				link: 'http://apretaste.me/profile/' + this.profile.username
			});
		},
		addImage: function () {
			this.$refs.gallery.addImage();
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
		},
		openBlockDrawer: function () {
			this.openDrawer('¿Está seguro de bloquear a <b>@' + this.profile.username +
				'</b>? Este usuario no podrá enviarle más solitudes de' +
				'amistad, ni mencionarle ni verá sus publicaciones', {
				icon: 'fa fa-ban',
				caption: 'Bloquear',
				onTap: this.blockUser
			});
		},
		openDeleteDrawer: function () {
			this.openDrawer('¿Está seguro de sacar a <b>@' + this.profile.username + '</b> de su lista de amigos?', {
				icon: 'fa fa-times',
				caption: 'Eliminar amigo',
				onTap: this.deleteFriend
			});
		},
		openRejectDrawer: function () {
			this.openDrawer('¿Rechazar solicitud de amistad de <b>@' + this.profile.username + '</b>?', {
				icon: 'fa fa-times',
				caption: 'Rechazar solicitud',
				onTap: this.rejectFriend
			});
		},
		openAcceptDrawer: function () {
			this.openDrawer('¿Aceptar solicitud de amistad de <b>@' + this.profile.username + '</b>?', {
				icon: 'fa fa-user-plus', caption: 'Aceptar solicitud',
				onTap: this.acceptFriend
			});
		},
		openCancelDrawer: function () {
			this.openDrawer('¿Cancelar solicitud de amistad para <b>@' + this.profile.username + '</b>?', {
				icon: 'fa fa-times', caption: 'Cancelar solicitud',
				onTap: this.cancelRequest
			});
		},
		openAddDrawer: function () {
			this.openDrawer('¿Agregar a <b>@' + this.profile.username + '</b> a sus amigos?', {
				icon: 'fa fa-user-plus', caption: 'Enviar solicitud',
				onTap: this.addFriend
			});
		},
		openChat: function () {
			apretaste.send({
				command: 'chat',
				data: {
					id: this.profile.id
				}
			});
		},
		blockUser: function () {
			this.type = 'blocked';

			apretaste.send({
				command: 'amigos bloquear',
				data: {id: this.profile.id},
				redirect: false
			});

			apretaste.send({command: 'AMIGOS BLOCKED', useCache: false});
		},
		deleteFriend: function () {
			this.type = 'none';

			apretaste.send({
				command: 'amigos eliminar',
				data: {id: this.profile.id},
				redirect: false
			});

			this.$refs.toast.show('Amigo eliminado');
		},
		rejectFriend: function () {
			this.type = 'none';

			apretaste.send({
				command: 'amigos rechazar',
				data: {id: this.profile.id},
				redirect: false
			});

			this.$refs.toast.show('Solicitud rechazada');
		},
		acceptFriend: function () {
			this.type = 'friends';

			apretaste.send({
				command: 'amigos agregar',
				data: {id: this.profile.id},
				redirect: false
			});

			this.$refs.toast.show('Solicitud aceptada');
		},
		cancelRequest: function () {
			this.type = 'none';

			apretaste.send({
				command: 'amigos rechazar',
				data: {id: this.profile.id},
				redirect: false
			});

			this.$refs.toast.show('Solicitud cancelada');
		},
		addFriend: function () {
			console.log('add frieeend');
			this.type = 'waiting';

			apretaste.send({
				command: 'amigos agregar',
				data: {id: this.profile.id},
				redirect: false
			});

			this.$refs.toast.show('Solicitud enviada');
		},
		openDrawer: function (title, option) {
			this.$refs.drawer.show(title, [option, {
				icon: 'fa fa-times', caption: 'Cerrar'
			}]);
		}
	}
}
</script>

<style scoped>

</style>