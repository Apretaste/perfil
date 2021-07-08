<!--suppress JSUnfilteredForInLoop -->
<template>
	<div class="row">
		<div class="col-12">
			<ap-list :data="mediaList"></ap-list>
		</div>
	</div>
</template>

<script>
module.exports = {
	name: "SocialMedia",
	props: ['profile'],
	data: function () {
		return {
			socialLinks: [
				{
					name: 'facebook',
					icon: 'fab fa-facebook-f',
					caption: 'facebook.com/',
					link: 'https://www.facebook.com/',
				}, {
					name: 'twitter',
					icon: 'fab fa-twitter',
					caption: 'twitter.com/',
					link: 'https://twitter.com/',
				}, {
					name: 'instagram',
					icon: 'fab fa-instagram',
					caption: 'instagram.com/',
					link: 'https://instagram.com/',
				}, {
					name: 'telegram',
					icon: 'fab fa-telegram-plane',
					caption: '@',
					link: 'https://t.me/',
				}, {
					name: 'whatsapp',
					icon: 'fab fa-whatsapp',
					caption: '+',
					link: 'https://api.whatsapp.com/send?phone=',
				}, {
					name: 'website',
					icon: 'fas fa-globe',
					link: ''
				}
			]
		};
	},
	computed: {
		mediaList: function () {
			const list = [];

			for (const key in this.socialLinks) {
				const socialLink = this.socialLinks[key];

				if (this.profile[socialLink.name]) {
					list.push(this.buildMedia(socialLink));
				}
			}

			return list;
		}
	},
	methods: {
		buildMedia: function (socialLink) {
			const media = this.profile[socialLink.name];
			const thisRef = this;

			return {
				icon: socialLink.icon,
				title: socialLink.caption + media,
				onTap: function () {
					thisRef.openMedia(socialLink.link + media)
				}
			};
		},
		openMedia: function (mediaUrl) {
			window.open(mediaUrl, '_blank');
		}
	}
}
</script>

<style scoped>

</style>