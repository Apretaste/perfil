"use strict";

// Variables

var selectedColor;

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
		img: 'level-zafiro.png',
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
		img: 'level-topacio.png',
		color: '#db055d',
		titleColor: '#ff046b',
		experience: 100,
		maxExperience: 299,
		benefits: [
			'Posibilidad de canjear recargas', 'Doble créditos al canjear un cupón'
		]
	},

	{
		name: "Rubí",
		img: 'level-rubi.png',
		color: '#db055d',
		titleColor: '#e20a0a',
		experience: 300,
		maxExperience: 499,
		benefits: [
			'Beneficios anteriores', '5 tickets gratis para la rifa mensual (Al inicio de cada mes)', 'Doble crédito al completar retos'
		]
	},
	{
		name: "Ópalo",
		img: 'level-opalo.png',
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
		img: 'level-esmeralda.png',
		color: '#07880b',
		titleColor: '#07880b',
		experience: 700,
		maxExperience: 999,
		benefits: [
			'Beneficios anteriores', '10% de descuento en todos los canjes', 'Doble créditos al terminar encuestas'
		]
	},
	{
		name: "Diamante",
		img: 'level-diamante.png',
		color: '#0080b9',
		titleColor: '#00b0fe',
		experience: 1000,
		maxExperience: 1000,
		benefits: [
			'Beneficios anteriores', 'Acceso al "Club Diamante"', '§1 de crédito cada mes'
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
		if ($('.mini-card .person-avatar').css('background-size') != 'auto') clearTimeout(resizeInterval);
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

	if (typeof cellphoneUpdateAllowed != "undefined" && !cellphoneUpdateAllowed) {
		var cellphone = $('#cellphone');
		cellphone.attr('disabled', '');
		cellphone.parent().click(function () {
			M.Modal.getInstance($('#cantUpdateCellphoneModal')).open();
		});
	}
});

// Main Functions

function resizeImg() {
	if (typeof profile == "undefined") return;
	$('.profile-img').css('height', '');

	var img = $('#profile-avatar');
	var size = $(window).height() / 4; // picture must be 1/4 of the screen

	img.height(size);
	img.width(size);

	img.css('top', -4 - $(window).height() / 8 + 'px'); // align the picture with the div

	$('#edit-fields, .profile-info').css('margin-top', 5 - $(window).height() / 6.5 + 'px'); // move the row before to the top to fill the empty space

	$('#img-pre').height(img.height() * 0.7); // set the height of the colored div after the photo
}

function getAvatar(avatar, resPath, size) {
	return "background-image: url(" + resPath + "/images/avatars/" + avatar + ".png);";
}

function genderColor(gender) {
	return profile.gender == "M" ? "dodgerblue" : profile.gender == "F" ? "#e61966" : "black-text";
}

function changeColor(color) {
	selectedColor = color;
	$('.mini-card .person-avatar').css('background-color', avatarColors[color]);
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

	if (experience >= 1000) userLevel.percent = 100;
	else userLevel.percent =
		userLevel.maxExperience !== 0
			? ((experience - userLevel.experience) / (userLevel.maxExperience - userLevel.experience)) * 100
			: 0;

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
	var data = {};
	names.forEach(function (prop) {
		if ($('#' + prop).val() != profile[prop] && $('#' + prop).val() != null && $('#' + prop).val() != "") {
			data[prop] = $('#' + prop).val();
			profile[prop] = $('#' + prop).val();
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
		'data': {'id': id},
		'redirect': true/*,
		'callback': {'name': 'deleteImageCallback'}*/
	});
}

function selectDefaultImage() {
	apretaste.send({
		'command': 'PERFIL FOTO',
		'data': {'id': id},
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
		$('input:file').val(null);
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
