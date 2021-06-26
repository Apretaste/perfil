<template>
	<div class="row">
		<div class="col-6 my-2" v-for="image in images">
			<ap-image v-bind="buildImg(image)"></ap-image>
		</div>
	</div>
</template>

<script>
module.exports = {
	name: "Gallery",
	props: ['images', 'canEdit'],
	methods: {
		buildImg: function (image) {
			return {
				src: image.file,
				actions: [{
					icon: 'fa fa-star', onTap: function () {
						this.onTapImg(image);
					}
				}]
			};
		},
		onTapImg: function (image) {
			if (this.canEdit) {
				// TODO confirm
				apretaste.send({
					command: 'PERFIL BORRAR',
					data: {id: image.id}
				});

				var imgIndex = this.images.indexOf(image);
				this.images.splice(imgIndex, 1)
			} else {
				// TODO
				// Open modal to write comment
				// Send to the chat
				// Show callback
			}
		}
	}
}
</script>

<style scoped>

</style>