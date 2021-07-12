<template>
	<div class="row justify-content-center my-4">
		<div class="col-4 position-relative">
			<ap-avatar :data="avatar"></ap-avatar>
			<ap-button id="editButton" :data="editButton"></ap-button>
		</div>
	</div>
</template>

<script>
module.exports = {
	// makes variable reactive in the parent, so when the event is emitted the parent variable changes too
	model: {
		prop: 'picture',
		event: 'change'
	},
	props: ['picture', 'isInfluencer'],
	data: function () {
		return {
			editButton: {
				icon: 'fa fa-edit',
				onTap: this.changePicture,
				rounded: true,
				size: 'small'
			}
		}
	},
	computed: {
		avatar: function () {
			const avatar = {
				size: window.innerWidth / 4,
				influencer: this.isInfluencer
			};

			if (this.picture != null) {
				avatar.picture = this.picture;
			} else {
				// If there's no picture use the placeholder
				avatar.picture = apretaste.componentsPath + 'images/picture-placeholder.png';
			}

			return avatar;
		}
	},
	methods: {
		changePicture: function () {
			apretaste.loadImage('vm.$refs.main.$refs.photoEdit.onImageLoaded');
		},
		onImageLoaded: function (path) {
			const basename = path.split(/[\\/]/).pop()
			apretaste.send({
				command: 'PERFIL FOTO',
				data: {pictureName: basename, updatePicture: true},
				redirect: false,
				files: [path],
				callback: {
					name: 'vm.$refs.main.$refs.photoEdit.onImageSubmit',
					data: path
				}
			});
		},
		onImageSubmit: function (newImage) {
			this.picture = newImage.split(/[\\/]/).pop();

			// This emits the change to the variable associated to the v-model in the parent
			this.$emit('change', this.picture)
		}
	}
}
</script>

<style scoped>
#editButton {
	position: absolute;
	bottom: 12px;
	right: 16px;
}
</style>