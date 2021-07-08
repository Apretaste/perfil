<template>
	<div class="row">
		<div class="col-12">
			<ap-text :data="{text: profile.aboutMe}"></ap-text>
			<ap-chip class="m-1" v-for="chip in chips" :data="chip"></ap-chip>
		</div>
	</div>
</template>

<script>
module.exports = {
	name: "ProfileDescription",
	props: ['profile'],
	data: function () {
		return {
			levels: [
				{
					name: "Zafiro",
					img: 'level-zafiro.png',
					color: '#0842b7',
					titleColor: '#0055ff',
					experience: 0,
					maxExperience: 99,
					benefits: ['Sin beneficios']
				},
				{
					name: "Topacio",
					img: 'level-topacio.png',
					color: '#db055d',
					titleColor: '#ff046b',
					experience: 100,
					maxExperience: 299,
					benefits: ['Posibilidad de canjear recargas', 'Doble créditos al canjear un cupón']
				},
				{
					name: "Rubí",
					img: 'level-rubi.png',
					color: '#db055d',
					titleColor: '#e20a0a',
					experience: 300,
					maxExperience: 499,
					benefits: ['Beneficios anteriores', 'Doble crédito al completar retos']
				},
				{
					name: "Ópalo",
					img: 'level-opalo.png',
					color: '#ff6c0a',
					titleColor: '#ff8800',
					experience: 500,
					maxExperience: 699,
					benefits: ['Beneficios anteriores', 'Acceso a nuevos servicios primero', 'Doble créditos al invitar a tus amig@s']
				},
				{
					name: "Esmeralda",
					img: 'level-esmeralda.png',
					color: '#07880b',
					titleColor: '#07880b',
					experience: 700,
					maxExperience: 999,
					benefits: ['Beneficios anteriores', 'Descuento del 10% al canjear', 'Doble créditos al terminar encuestas']
				},
				{
					name: "Diamante",
					img: 'level-diamante.png',
					color: '#0080b9',
					titleColor: '#00b0fe',
					experience: 1000,
					maxExperience: 1000,
					benefits: ['Beneficios anteriores', 'Acceso al "Club Diamante"', '§1 de crédito cada mes']
				},
			]
		};
	},
	computed: {
		userLevel: function () {
			const experience = this.profile.experience;
			var userLevel;

			this.levels.forEach(function (level) {
				if (experience >= level.experience) userLevel = level;
			});

			if (experience >= 1000) userLevel.percent = 100;
			else userLevel.percent =
				userLevel.maxExperience !== 0
					? ((experience - userLevel.experience) / (userLevel.maxExperience - userLevel.experience)) * 100
					: 0;

			return userLevel;
		},
		chips: function () {
			var chips = [];

			chips.push(this.buildChip(
				this.profile.friendList + ' Amigos',
				'fa fa-user-friends'
			));

			chips.push(this.buildChip(
				'#' + this.profile.ranking,
				'fa fa-trophy'
			));

			chips.push(this.buildChip(
				this.profile.experience + ' (' + this.userLevel.name + ')',
				'fa fa-bolt',
				this.openLevelsHelp
			));

			for (const i in this.profile.profile_tags) {
				const tag = this.profile.profile_tags[i];

				chips.push(this.buildChip(tag));
			}

			for (const i in this.profile.profession_tags) {
				const tag = this.profile.profession_tags[i];

				chips.push(this.buildChip(tag));
			}

			for (const i in this.profile.location_tags) {
				const tag = this.profile.location_tags[i];

				chips.push(this.buildChip(tag));
			}

			return chips;
		},
	},
	methods: {
		buildChip: function (text, icon, onTap) {
			const chip = {icon: icon, text: text};

			if (onTap != null) {
				chip.onTap = onTap;
			}

			return chip;
		},
		openLevelsHelp: function () {
			apretaste.send({command: 'perfil niveles'});
		}
	}
}
</script>

<style scoped>

</style>