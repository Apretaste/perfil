<template>
	<div class="row">
		<div class="col-6 my-2" v-for="image in images">
			<ap-image v-bind="buildImg(image)"></ap-image>
		</div>

		<div v-if="canEdit" class="col-6 my-2" id="add-img" @click="addImage">
			<i class="fa fa-plus fa-lg" style="font-size: 2rem"></i>
			<span>Añadir foto</span>
		</div>

		<ap-drawer
			:data="confirmDialogData"
			ref="imgDeleteDialog"
		></ap-drawer>
		<ap-input-drawer
			:data="replyDialogData"
			ref="replyDialog"
		></ap-input-drawer>

		<ap-toast ref="toast"></ap-toast>
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
	data: function () {
		return {
			confirmDialogData: {
				id: 'confirmDelete',
				options: [{
					icon: 'fa fa-trash-alt', caption: 'Confirmar',
					onTap: this.deleteImg
				}]
			},
			replyDialogData: {
				maxLength: 250,
				icon: 'fa fa-comment',
				label: 'Mensaje privado',
				isArea: true,
				onComplete: this.sendImgReply
			},
			imgToDelete: null,
			imgToReply: null
		}
	},
	methods: {
		buildImg: function (image) {
			const refsToThis = this;

			const imgData = {
				src: image.file,
				actions: [],
				square: true
			};

			if (this.canEdit) {
				imgData.actions.push({
					icon: 'fa fa-trash', onTap: function () {
						refsToThis.confirmDelete(image);
					}
				});
			} else {
				imgData.actions.push({
					icon: 'fa fa-comment', onTap: function () {
						refsToThis.showReplyModal(image);
					}
				});
			}

			return imgData;
		},
		showReplyModal: function (image) {
			this.imgToReply = image;
			this.$refs.replyDialog.show();
		},
		sendImgReply: function (msg) {
			apretaste.send({
				command: 'CHAT PERFILIMAGE',
				data: {
					message: msg,
					image: this.imgToReply.id
				},
				redirect: false
			});

			this.$refs.toast.show('Mensaje enviado');
		},
		confirmDelete: function (image) {
			this.imgToDelete = image;
			this.$refs.imgDeleteDialog.show();
		},
		deleteImg: function () {
			const image = this.imgToDelete;

			if (this.canEdit) {
				apretaste.send({
					command: 'PERFIL BORRAR',
					data: {id: image.id},
					redirect: false
				});

				var imgIndex = this.images.indexOf(image);
				this.images.splice(imgIndex, 1);

				this.$refs.toast.show('Imagen eliminada');
			}
		},
		addImage: function () {
			if (this.images.length >= 6) {
				this.$refs.toast.show('Solo se permiten 6 imagenes');
				return;
			}

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
#add-img {
	text-align: center;
	position: relative;
	height: 150px;
}

#add-img i {
	position: absolute;
	top: calc(50% - 2rem);
	font-size: 2rem;
	transform: translate(-50%);
}

#add-img span {
	position: absolute;
	top: 50%;
	transform: translate(-50%);
}
</style>