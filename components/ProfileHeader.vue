<template>
	<div class="row" :style="{height: totalSize}">
		<div :style="{height: topBgSize}" class="first-bg"></div>
		<ap-avatar id="avatar" :style="avatarPosition" :data="avatar"></ap-avatar>
		<ap-text
			:style="textPosition"
			id="username"
			class="text-center"
			:class="profile.gender"
			:data="{text: '@'+profile.username}"
		></ap-text>
	</div>
</template>

<script>
module.exports = {
	name: "ProfileHeader",
	props: ['profile'],
	data: function () {
		const avatarSize = (window.innerHeight / 4) + 8;
		const avatarPosition = {top: (avatarSize / 4) + 'px'};
		const textPosition = {top: (avatarSize * 1.25) + 'px'}

		return {
			avatarSize: avatarSize,
			avatarPosition: avatarPosition,
			textPosition: textPosition,
			topBgSize: (avatarSize * 0.75) + 'px',
			totalSize: (avatarSize * 1.4) + 'px'
		};
	},
	computed: {
		avatar: function () {
			const avatar = {
				letter: this.profile.username[0],
				size: this.avatarSize,
				online: false,
				influencer: this.profile.isInfluencer
			};

			if (this.profile.isInfluencer) {
				avatar.picture = apretaste.componentsPath + 'images/influencers/' + this.username + '.png';
			} else {
				avatar.picture = this.profile.picture;
			}

			return avatar;
		}
	}
}
</script>

<style scoped>
#avatar {
	left: 50%;
	transform: translateX(-50%);
	position: absolute;
	border: 4px solid white;
}

#username {
	position: absolute;
}
</style>