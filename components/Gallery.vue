<template>
	<div class="row">
		<div class="col-6 my-2" v-for="image in images">
			<ap-image v-bind="buildImg(image)"></ap-image>
		</div>

		<div v-if="images.length < 6 && canEdit" class="col-6 my-2" @click="addImage">
			<i class="fa fa-plus fa-lg"></i>
		</div>
	</div>
</template>

<script>
module.exports = {
	name: "Gallery",
	// makes variable reactive in the parent, so when the event is emitted the parent variable changes too
	model: {
		prop: 'images',
		event: 'change'
	},
	props: ['images', 'canEdit'],
	methods: {
		buildImg: function (image) {
			const refsToThis = this;

			const imgData = {
				src: image.file,
				actions: []
			};

			if (this.canEdit) {
				imgData.actions.push({
					icon: 'fa fa-trash', onTap: function () {
						refsToThis.deleteImg(image);
					}
				});
			} else {
				imgData.actions.push({
					icon: 'fa fa-comment', onTap: function () {
						refsToThis.showMsgModal(image);
					}
				});
			}

			return imgData;
		},
		showMsgModal: function (image) {

		},
		deleteImg: function (image) {
			if (this.canEdit) {
				// TODO confirm
				apretaste.send({
					command: 'PERFIL BORRAR',
					data: {id: image.id},
					redirect: false
				});

				var imgIndex = this.images.indexOf(image);
				this.images.splice(imgIndex, 1)
			} else {
				// TODO
				// Open modal to write comment
				// Send to the chat
				// Show callback
			}
		},
		addImage: function () {
			apretaste.loadImage('vm.$refs.main.$refs.gallery.onImageLoaded');
		},
		onImageLoaded: function (path) {
			const basename = path.split(/[\\/]/).pop()
			apretaste.send({
				command: 'PERFIL FOTO',
				data: {pictureName: basename},
				redirect: false,
				async: true,
				files: [path],
				callback: {
					name: 'vm.$refs.main.$refs.gallery.onImageSubmit'
				}
			});
		},
		onImageSubmit: function (data) {
			this.images.push(data);

			// This emits the change to the variable associated to the v-model in the parent
			this.$emit('change', this.images)
		}
	}
}
</script>

<style scoped>

</style>