<template>
	<div class="row my-3">
		<div class="col-12 d-flex">
			<ap-image v-bind="levelImage"></ap-image>
			<div class="ms-1 w-100">
				<ap-chip :data="expChip"></ap-chip>
				<div class="progress">
					<div
						class="progress-bar"
						role="progressbar"
						:style="{width: userLevel.percent+'%', backgroundColor:userLevel.color}"
						:aria-valuenow="userLevel.percent"
						aria-valuemin="0"
						aria-valuemax="100"
					></div>
				</div>
			</div>
		</div>

		<div class="col-12">
			<ap-text
				:data="{text: 'Mientras más utilices la app, mayor experiencia ganarás, ' +
				 'y al subir de nivel podrás disfrutar de mayores beneficios.'}"
			></ap-text>

			<level-tile v-for="level in levels" :level="level"></level-tile>
		</div>

		<ap-text class="text-center" :data="{text: '¿Cómo gano experiencia?'}" @click.native="openHelp"></ap-text>

		<div class="col-12">
			<ap-fab :data="[{icon: 'fas fa-arrow-left', onTap: apretaste.back}]"></ap-fab>
		</div>
	</div>
</template>

<script>
module.exports = {
	components: {
		'LevelTile': httpVueLoader(apretaste.servicePath + 'components/LevelTile.vue')
	},
	data: function () {
		const state = apretaste.request;

		state.levels = [
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
		];

		return state;
	},
	computed: {
		userLevel: function () {
			var userLevel;

			for (const i in this.levels) {
				const level = this.levels[i];

				if (this.experience >= level.experience) userLevel = level;
			}

			if (this.experience >= 1000) userLevel.percent = 100;
			else userLevel.percent =
				userLevel.maxExperience !== 0
					? ((this.experience - userLevel.experience) / (userLevel.maxExperience - userLevel.experience)) * 100
					: 0;

			return userLevel;
		},
		expChip: function () {
			return {
				clear: true,
				text: this.experience + ' ' + this.userLevel.name,
				icon: 'fa fa-bolt'
			};
		},
		levelImage: function () {
			return {
				src: apretaste.servicePath + '/images/' + this.userLevel.img,
				size: 50
			};
		}
	},
	methods: {
		openHelp: function () {
			apretaste.send({command: 'PERFIL EXPERIENCIA'});
		}
	}
}
</script>

<style scoped>
.progress{
	height: 6px;
}
</style>