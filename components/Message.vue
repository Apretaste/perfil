<template>
	<ap-message :data="msgData"></ap-message>
</template>

<script>
module.exports = {
	data: function () {
		return {msgData: apretaste.request};
	}
}
</script>

<style scoped>

</style>