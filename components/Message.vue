<template>
	<div class="row">
		<div class="col-12 text-center">
			<h1>{{ header }}</h1>
			<i class="fa-lg" :class="icon"></i>

			<ap-text :data="{text: text}"></ap-text>

			<ap-button
				v-if="button"
				:data="{caption: button.caption, size: 'large', onTap: btnPress}"
			></ap-button>

			<ap-button
				v-else
				:data="{caption: 'Atras', size: 'large', onTap: apretaste.back}"
			></ap-button>
		</div>
	</div>
</template>

<script>
module.exports = {
	data: function () {
		return this.apretaste.request;
	},
	methods: {
		btnPress: function () {
			apretaste.send({command: this.button.command, data: this.button.data});
		}
	}
}
</script>

<style scoped>
p {
	margin: 30px 0;
}
</style>