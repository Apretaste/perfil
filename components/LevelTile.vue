<template>
	<div class="border border-2 p-1 rounded-3 my-2 d-flex">
		<ap-avatar
			:data="{picture: apretaste.servicePath + 'images/'+level.img, size: 40}"
		></ap-avatar>

		<div class="d-inline-block ms-1">
			<span :style="{color: level.titleColor}">{{ level.name }}</span>
			<ap-chip :data="{icon: 'fa fa-bolt', clear: true, text: '(' + level.experience + ' o más)'}"></ap-chip>
			<div v-for="benefit in level.benefits">
				<span>• {{ benefit }}.</span>
			</div>
		</div>
	</div>
</template>

<script>
module.exports = {
	props: ['level'],

}
</script>

<style scoped>

</style>