<template>
	<div class="row my-3">
		<ap-text :data="topText"></ap-text>

		<!-- experience -->
		<div class="col-12 mt-4">
			<ap-list :data="listData"></ap-list>
		</div>

		<div class="col-12">
			<ap-fab :data="[{icon: 'fas fa-arrow-left', onTap: apretaste.back}]"></ap-fab>
		</div>
	</div>
</template>

<script>
module.exports = {
	data: function () {
		const state = apretaste.request;

		state.topText = {
			text: 'A continuación las maneras de ganar experiencia y la cantidad y concepto por cada una.'
		};

		state.listData = state.experience.map(function (experience) {
			return {
				leadingText: experience.value,
				text: experience.description,
				chips: [{icon: 'fas fa-' + experience.conceptIcon, text: experience.conceptCaption}]
			};
		});

		return apretaste.request;
	}
}
</script>

<style scoped>

</style>