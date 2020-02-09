"use strict";

// Variables

var colors = {
	'azul': '#99F9FF',
	'verde': '#9ADB05',
	'rojo': '#FF415B',
	'morado': '#58235E',
	'naranja': '#F38200',
	'amarillo': '#FFE600'
};

var selectedColor;

var avatars = {
	apretin: {caption: "Apretín", gender: 'M'},
	apretina: {caption: "Apretina", gender: 'F'},
	artista: {caption: "Artista", gender: 'M'},
	bandido: {caption: "Bandido", gender: 'M'},
	belleza: {caption: "Belleza", gender: 'F'},
	chica: {caption: "Chica", gender: 'F'},
	coqueta: {caption: "Coqueta", gender: 'F'},
	cresta: {caption: "Cresta", gender: 'M'},
	deportiva: {caption: "Deportiva", gender: 'F'},
	dulce: {caption: "Dulce", gender: 'F'},
	emo: {caption: "Emo", gender: 'M'},
	oculto: {caption: "Oculto", gender: 'M'},
	extranna: {caption: "Extraña", gender: 'F'},
	fabulosa: {caption: "Fabulosa", gender: 'F'},
	fuerte: {caption: "Fuerte", gender: 'M'},
	ganadero: {caption: "Ganadero", gender: 'M'},
	geek: {caption: "Geek", gender: 'F'},
	genia: {caption: "Genia", gender: 'F'},
	gotica: {caption: "Gótica", gender: 'F'},
	gotico: {caption: "Gótico", gender: 'M'},
	guapo: {caption: "Guapo", gender: 'M'},
	hawaiano: {caption: "Hawaiano", gender: 'M'},
	hippie: {caption: "Hippie", gender: 'M'},
	hombre: {caption: "Hombre", gender: 'M'},
	atengo: {caption: "Atengo", gender: 'M'},
	libre: {caption: "Libre", gender: 'F'},
	jefe: {caption: "Jefe", gender: 'M'},
	jugadora: {caption: "Jugadora", gender: 'F'},
	mago: {caption: "Mago", gender: 'M'},
	metalero: {caption: "Metalero", gender: 'M'},
	modelo: {caption: "Modelo", gender: 'F'},
	moderna: {caption: "Moderna", gender: 'F'},
	musico: {caption: "Músico", gender: 'M'},
	nerd: {caption: "Nerd", gender: 'M'},
	punk: {caption: "Punk", gender: 'M'},
	punkie: {caption: "Punkie", gender: 'M'},
	rap: {caption: "Rap", gender: 'M'},
	rapear: {caption: "Rapear", gender: 'M'},
	rapero: {caption: "Rapero", gender: 'M'},
	rock: {caption: "Rock", gender: 'M'},
	rockera: {caption: "Rockera", gender: 'F'},
	rubia: {caption: "Rubia", gender: 'F'},
	rudo: {caption: "Rudo", gender: 'M'},
	sencilla: {caption: "Sencilla", gender: 'F'},
	sencillo: {caption: "Sencillo", gender: 'M'},
	sennor: {caption: "Señor", gender: 'M'},
	sennorita: {caption: "Señorita", gender: 'F'},
	sensei: {caption: "Sensei", gender: 'M'},
	surfista: {caption: "Surfista", gender: 'M'},
	tablista: {caption: "Tablista", gender: 'F'},
	vaquera: {caption: "Vaquera", gender: 'F'}
};

var countries = [{
	code: 'cu',
	name: 'Cuba'
}, {
	code: 'us',
	name: 'Estados Unidos'
}, {
	code: 'es',
	name: 'España'
}, {
	code: 'it',
	name: 'Italia'
}, {
	code: 'mx',
	name: 'Mexico'
}, {
	code: 'br',
	name: 'Brasil'
}, {
	code: 'ec',
	name: 'Ecuador'
}, {
	code: 'ca',
	name: 'Canada'
}, {
	code: 'vz',
	name: 'Venezuela'
}, {
	code: 'al',
	name: 'Alemania'
}, {
	code: 'co',
	name: 'Colombia'
}, {
	code: 'OTRO',
	name: 'Otro'
}];

var province = {
	'PINAR_DEL_RIO': 'Pinar del Río',
	'ARTEMISA': 'Artemisa',
	'LA_HABANA': 'La Habana',
	'MAYABEQUE': 'Mayabeque',
	'MATANZAS': 'Matanzas',
	'CIENFUEGOS': 'Cienfuegos',
	'VILLA_CLARA': 'Villa Clara',
	'SANCTI_SPIRITUS': 'Sancti Spíritus',
	'CIEGO_DE_AVILA': 'Ciego de Ávila',
	'CAMAGUEY': 'Camagüey',
	'LAS_TUNAS': 'Las Tunas',
	'GRANMA': 'Granma',
	'HOLGUIN': 'Holguín',
	'SANTIAGO_DE_CUBA': 'Santiago de Cuba',
	'GUANTANAMO': 'Guantánamo',
	'ISLA_DE_LA_JUVENTUD': 'Isla de la Juventud'
};

var levels = [
	{
		name: "Zafiro",
		img: 'Zafiro',
		color: '#0842b7',
		titleColor: '#0055ff',
		experience: 0,
		maxExperience: 99,
		benefits: [
			'Sin beneficios'
		]
	},
	{
		name: "Topacio",
		img: 'Topacio',
		color: '#db055d',
		titleColor: '#ff046b',
		experience: 100,
		maxExperience: 299,
		benefits: [
			'Posibilidad de transferir crédito', 'Doble créditos al canjear un cupón'
		]
	},

	{
		name: "Rubí",
		img: 'Rubi',
		color: '#db055d',
		titleColor: '#e20a0a',
		experience: 300,
		maxExperience: 499,
		benefits: [
			'Beneficios anteriores', '5 tickets gratis para la rifa mensual', 'Doble crédito al completar retos'
		]
	},
	{
		name: "Ópalo",
		img: 'Opalo',
		color: '#ff6c0a',
		titleColor: '#ff8800',
		experience: 500,
		maxExperience: 699,
		benefits: [
			'Beneficios anteriores', 'Acceso a nuevos servicios primero', 'Doble créditos al invitar a tus amig@s'
		]
	},
	{
		name: "Esmeralda",
		img: 'Esmeralda',
		color: '#07880b',
		titleColor: '#07880b',
		experience: 700,
		maxExperience: 999,
		benefits: [
			'Beneficios anteriores', '10% de descuento en todas las compras', 'Doble créditos al terminar encuestas'
		]
	},
	{
		name: "Diamante",
		img: 'Diamante',
		color: '#0080b9',
		titleColor: '#00b0fe',
		experience: 1000,
		maxExperience: 1000,
		benefits: [
			'Beneficios anteriores', 'Acceso al "Club Diamante"', '§1 de crédito gratis al mes'
		]
	},
];

// Doc Ready Function

$(document).ready(function () {
	$('.tabs').tabs();
	$('select').formSelect();
	$('.modal').modal();
	$('#about_me, #city').characterCounter();

	var resizeInterval = setInterval(function () {
		// check until the img has the correct size
		resizeImg();
		if ($('#profile-rounded-img').css('background-size') != 'auto') clearTimeout(resizeInterval);
	}, 1);

	if (typeof profile != "undefined") {
		var interests = [];
		profile.interests.forEach(function (interest) {
			interests.push({tag: interest});
		});
		profile.interests = JSON.stringify(interests);
		$('.chips').chips();
		$('.chips-initial').chips({data: interests});
	}

	showStateOrProvince();
});

// Main Functions

function resizeImg() {
	if (typeof profile == "undefined") return;
	$('.profile-img').css('height', '');

	var img = $('#profile-rounded-img');
	var size = $(window).height() / 4; // picture must be 1/4 of the screen

	img.height(size);
	img.width(size);

	img.css('top', -4 - $(window).height() / 8 + 'px'); // align the picture with the div

	$('#edit-fields, .profile-info').css('margin-top', 5 - $(window).height() / 6.5 + 'px'); // move the row before to the top to fill the empty space

	$('#img-pre').height(img.height() * 0.7); // set the height of the colored div after the photo
}

function getAvatar(avatar, serviceImgPath) {
	return "background-image: url(" + serviceImgPath + "/" + avatar + ".png);";
}

function genderColor(gender) {
	return profile.gender == "M" ? "dodgerblue" : profile.gender == "F" ? "#e61966" : "black-text";
}

function changeColor(color) {
	selectedColor = color;
	$('.mini-card div.avatar').css('background-color', colors[color]);
}

function showStateOrProvince() {
	var country = $('#country').val();
	var province = $('.province-div');
	var usstate = $('.usstate-div');

	switch (country) {
		case 'cu':
			province.show();
			usstate.hide();
			break;

		case 'us':
			usstate.show();
			province.hide();
			break;

		default:
			usstate.hide();
			province.hide();
			break;
	}
}

function getUserLevel(experience) {
	var userLevel;
	levels.forEach(function (level) {
		if (experience >= level.experience) userLevel = level;
	});

	userLevel.percent = userLevel.maxExperience !== 0 ? ((experience - userLevel.experience) / (userLevel.maxExperience - userLevel.experience)) * 100 : 0;

	return userLevel;
}

function uploadPicture() {
	loadFileToBase64();
}

function showToast(text) {
	M.toast({html: text});
}

// Request Functions

function submitProfileData() {
	// array of possible values
	var names = ['first_name', 'last_name', 'about_me', 'country', 'year_of_birth', 'month_of_birth', 'day_of_birth', 'province', 'city', 'gender', 'sexual_orientation', 'marital_status', 'religion', 'body_type', 'eyes', 'skin', 'hair', 'highest_school_level', 'occupation', 'cellphone'];

	// create object to send to the backend
	var data = new Object();
	names.forEach(function (prop) {
		if ($('#' + prop).val() != profile[prop] && $('#' + prop).val() != null && $('#' + prop).val() != "") {
			data[prop] = $('#' + prop).val();
		}
	});

	// add interest to data object
	if (profile.interests != JSON.stringify(M.Chips.getInstance($('.chips')).chipsData)) {
		data['interests'] = M.Chips.getInstance($('.chips')).chipsData;
	}

	// add username to data object
	var cleanUsername = $('#username').val().replace('@', '');
	if (cleanUsername != profile.username) data.username = cleanUsername;

	// do not send empty petitions
	if ($.isEmptyObject(data)) {
		showToast("Usted no ha hecho ningún cambio");
		return false;
	}

	// save changes in the database
	apretaste.send({
		"command": "PERFIL UPDATE",
		"data": data,
		"redirect": false
	});

	// show success alert
	showToast("Sus cambios han sido guardados");
	return true;
}

function setAvatar(avatar) {
	if (typeof selectedColor == "undefined") selectedColor = currentColor;
	apretaste.send({
		'command': 'PERFIL UPDATE',
		'data': {
			'avatar': avatar,
			'avatarColor': selectedColor
		},
		'redirect': false,
		'callback': {
			'name': 'setAvatarCallback'
		}
	});
}

function deleteImage() {
	apretaste.send({
		'command': 'PERFIL BORRAR',
		'data': {'id': image.id},
		'redirect': true/*,
		'callback': {'name': 'deleteImageCallback'}*/
	});
}

function selectDefaultImage() {
	apretaste.send({
		'command': 'PERFIL FOTO',
		'data': {'id': image.id},
		'redirect': false,
		'callback': {'name': 'showToast', 'data': 'Imagen principal cambiada'}
	});
}

/*
function deleteImageCallback() {
	apretaste.send({'command': 'PERFIL IMAGENES'});
}*/

function setAvatarCallback() {
	apretaste.send({
		'command': 'PERFIL'
	});
}

function sendFile(base64File) {
	if (base64File.length > 2584000) {
		showToast("Imagen demasiado pesada");
		return;
	}

	apretaste.send({
		"command": "PERFIL FOTO",
		"data": {
			'picture': base64File
		},
		"redirect": false,
		"callback": {
			"name": "updatePicture",
			"data": base64File
		}
	});
}

// save changes to the origin
function changeOrigin() {
	var origin = $('#origin').val();

	apretaste.send({
		"command": "PERFIL UPDATE",
		"data": {"origin": origin},
		"redirect": false
	});

	showToast('¡Gracias por opinar!');
}

// Callback Functions

function updatePicture(file) {
	// add the picture to the gallery
	var imgElement =
		"<div class=\"col s6 m3 l2 image\"" +
		"	onclick=\"apretaste.send({'command': 'PERFIL VER', 'data': {'id': 'last'}})\">" +
		"	<img src=\"data:image/jpg;base64," + file + "\" class=\"responsive-img\" width=\"100%\"" +
		"	style=\"border-radius: 8px\">" +
		"</div>"

	if (images.length == 0) $('#gallery > div.col.s12').remove();
	$('#gallery').append(imgElement);

	showToast('Imagen agregada a la galeria');
}

// Prototype Functions

String.prototype.firstUpper = function () {
	return this.charAt(0).toUpperCase() + this.substr(1).toLowerCase();
};

// Polyfill Functions

// get an array with a range of numbers
function range(start, stop) {
	var a = [start], b = start;
	while (b < stop) a.push(++b);
	return a;
}

function _typeof(obj) {
	if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
		_typeof = function _typeof(obj) {
			return typeof obj;
		};
	} else {
		_typeof = function _typeof(obj) {
			return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
		};
	}
	return _typeof(obj);
}

if (!Object.keys) {
	Object.keys = function () {
		'use strict';

		var hasOwnProperty = Object.prototype.hasOwnProperty,
			hasDontEnumBug = !{
				toString: null
			}.propertyIsEnumerable('toString'),
			dontEnums = ['toString', 'toLocaleString', 'valueOf', 'hasOwnProperty', 'isPrototypeOf', 'propertyIsEnumerable', 'constructor'],
			dontEnumsLength = dontEnums.length;
		return function (obj) {
			if (_typeof(obj) !== 'object' && (typeof obj !== 'function' || obj === null)) {
				throw new TypeError('Object.keys called on non-object');
			}

			var result = [],
				prop,
				i;

			for (prop in obj) {
				if (hasOwnProperty.call(obj, prop)) {
					result.push(prop);
				}
			}

			if (hasDontEnumBug) {
				for (i = 0; i < dontEnumsLength; i++) {
					if (hasOwnProperty.call(obj, dontEnums[i])) {
						result.push(dontEnums[i]);
					}
				}
			}

			return result;
		};
	}();
}
