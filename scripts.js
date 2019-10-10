"use strict";

$(document).ready(function () {
	//For main profile
	$('.materialboxed').materialbox();

	if (typeof profile != "undefined" && typeof ownProfile != "undefined") {
		if (ownProfile) {
			$("#editar").click(function () {
				return apretaste.send({"command": "PERFIL EDITAR"});
			});
			$("#credito").click(function () {
				apretaste.send({"command": 'CREDITO'});
			});
			$("#rifa").click(function () {
				apretaste.send({"command": 'RIFA'});
			});
		} else {
			$("#chat").click(function () {
				apretaste.send({"command": 'CHAT', data: {"userId": profile.id}});
			});
			$("#bloquear").click(function () {
				apretaste.send({"command": 'PERFIL BLOQUEAR', data: {"username": profile.username}});
			});
		}
	} //For origin

	if (typeof origin != "undefined") {
		$('select').formSelect();
		$('#origin').change(function (event) {
			var selected = $('#origin option:selected').val();
			return apretaste.send({
				"command": "PERFIL UPDATE",
				"data": {"origin": selected},
				"redirect": false,
				"callback": !$('#picture').length ? {
					"name": "reloadOrigin",
					"data": selected
				} : false
			});
		});
		$('#origin option[value="' + origin + '"]').prop("selected", true);
	} //For profile edit

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

	$(window).resize(function () {
		return resizeImg();
	});

	var resizeInterval = setInterval(function () {
		// check until the img has the correct size
		resizeImg();
		if ($('#profile-rounded-img').css('background-size') != 'auto') clearTimeout(resizeInterval);
	}, 1);
});

function submitProfileData() {
	// array of possible values
	var names = ['first_name', 'last_name', 'country', 'year_of_birth', 'month_of_birth', 'day_of_birth', 'province', 'city', 'gender', 'sexual_orientation', 'marital_status', 'religion', 'body_type', 'eyes', 'skin', 'hair', 'highest_school_level', 'occupation', 'origin', 'cellphone'];

	// create object to send to the backend
	var data = new Object();
	names.forEach(function (prop) {
		if ($('#' + prop).val() != profile[prop] && $('#' + prop).val() != null) {
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
		showToast("Usted no ha hecho ningun cambio");
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

String.prototype.firstUpper = function () {
	return this.charAt(0).toUpperCase() + this.substr(1).toLowerCase();
};

function getCountries() {
	return [{
		code: 'cu',
		name: 'Cuba'
	}, {
		code: 'us',
		name: 'Estados Unidos'
	}, {
		code: 'es',
		name: 'Espana'
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
}

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

function resizeImg() {
	if (typeof profile == "undefined") return;
	$('.profile-img').css('height', '');

	var img = $('#profile-rounded-img');
	var size = $(window).height() / 4; // picture must be 1/4 of the screen

	img.height(size);
	img.width(size);
	var src = img.css('background-image');
	src = src.search('url') == 0 ? src.replace('url("', '').replace('")', '') : src;
	var bg = new Image();
	bg.src = src;

	if (bg.height >= bg.width) {
		var scale = bg.height / bg.width;
		img.css('background-size', size + 'px ' + size * scale + 'px');
	} else {
		var scale = bg.width / bg.height;
		img.css('background-size', size * scale + 'px ' + size + 'px');
	}

	img.css('top', -4 - $(window).height() / 8 + 'px'); // align the picture with the div

	$('#edit-fields').css('margin-top', 5 - $(window).height() / 8 + 'px'); // move the row before to the top to fill the empty space

	$('#img-pre').height(img.height() * 0.8); // set the height of the colored div after the photo
}

function reloadOrigin(origin) {
	$("#actual").html(origin);
	showToast('Respuesta guardada');
}

function showToast(text) {
	M.toast({html: text});
}

function uploadPicture() {
	loadFileToBase64();
}

function sendFile(base64File) {
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

function updatePicture(file) {
	// display the picture on the img
	$('#profile-rounded-img').css('background-image', "url(data:image/jpg;base64," + file + ')');
	resizeImg(); // show confirmation text

	showToast('Su foto ha sido cambiada correctamente');
}

// POLYFILL

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

// get an array with a range of numbers
function range(start, stop) {
	var a = [start], b = start;
	while (b < stop) a.push(++b);
	return a;
}