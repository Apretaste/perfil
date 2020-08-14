"use strict";

// Variables

var selectedColor;

// list of levels
var levels = [
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
		benefits: ['Beneficios anteriores', '5 tickets gratis para la rifa mensual (Al inicio de cada mes)', 'Doble crédito al completar retos']
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
		benefits: ['Beneficios anteriores', '10% de descuento en todos los canjes', 'Doble créditos al terminar encuestas']
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

var props = {
	fullName: {caption: 'Nombre y Apellido', icon: 'person', values: {}},
	gender: {caption: 'Género', icon: 'person', values: {'M': 'Masculino', 'F': 'Femenino'}},
	sexualOrientation: {
		caption: 'Orientación sexual',
		icon: 'favorite',
		values: {'HETERO': 'Heterosexual', 'HOMO': 'Homosexual', 'BI': 'Bisexual'}
	},
	dayOfBirth: {caption: 'Día de nacimiento', icon: 'cake', values: {}},
	monthOfBirth: {caption: 'Mes de nacimiento', icon: 'cake', values: {}},
	yearOfBirth: {caption: 'Año de nacimiento', icon: 'cake', values: {}},
	body: {
		caption: 'Cuerpo',
		icon: 'face',
		values: {'DELGADO': 'Delgado', 'MEDIO': 'Medio', 'EXTRA': 'Extra', 'ATLETICO': 'Atlético'}
	},
	eyes: {
		caption: 'Ojos', icon: 'face', values: {
			'NEGRO': 'Negro',
			'CARMELITA': 'Carmelita',
			'VERDE': 'Verde',
			'AZUL': 'Azul',
			'AVELLANA': 'Avellana',
			'OTRO': 'Otro'
		}
	},
	hair: {
		caption: 'Cabello', icon: 'face', values: {
			'TRIGUENO': 'Trigueño',
			'CASTANO': 'Castaño',
			'RUBIO': 'Rubio',
			'NEGRO': 'Negro',
			'ROJO': 'Rojo',
			'BLANCO': 'Blanco',
			'OTRO': 'Otro'
		}
	},
	skin: {
		caption: 'Piel',
		icon: 'face',
		values: {'NEGRO': 'Negro', 'BLANCO': 'Blanco', 'MESTIZO': 'Mestizo', 'OTRO': 'Otro'}
	},
	maritalStatus: {
		caption: 'Estado Civil', icon: 'favorite', values: {
			'SOLTERO': 'Soltero',
			'SALIENDO': 'Saliendo',
			'COMPROMETIDO': 'Comprometido',
			'CASADO': 'Casado',
			'DIVORCIADO': 'Divorciado',
			'VIUDO': 'Viudo'
		}
	},
	education: {
		caption: 'Nivel Educativo', icon: 'school', values: {
			'PRIMARIO': 'Primario',
			'SECUNDARIO': 'Secundario',
			'TECNICO': 'Técnico',
			'UNIVERSITARIO': 'Universitario',
			'POSTGRADUADO': 'Postgraduado',
			'DOCTORADO': 'Doctorado',
			'OTRO': 'Otro'
		}
	},
	occupation: {
		caption: 'Ocupación', icon: 'business_center', values: {
			'AMA_DE_CASA': 'Ama de casa',
			'ESTUDIANTE': 'Estudiante',
			'EMPLEADO_PRIVADO': 'Empleado Privado',
			'EMPLEADO_ESTATAL': 'Empleado Estatal',
			'INDEPENDIENTE': 'Trabajador Independiente',
			'JUBILADO': 'Jubilado',
			'DESEMPLEADO': 'Desempleado'
		}
	},
	country: {caption: 'País', icon: 'place', values: {}},
	province: {
		caption: 'Provincia', icon: 'place', values: {
			'PINAR_DEL_RIO': 'Pinar del Río',
			'LA_HABANA': 'La Habana',
			'ARTEMISA': 'Artemisa',
			'MAYABEQUE': 'Mayabeque',
			'MATANZAS': 'Matanzas',
			'VILLA_CLARA': 'Villa Clara',
			'CIENFUEGOS': 'Cienfuegos',
			'SANCTI_SPIRITUS': 'Sancti Spiritus',
			'CIEGO_DE_AVILA': 'Ciego de Ávila',
			'CAMAGUEY': 'Camagüey',
			'LAS_TUNAS': 'Las Tunas',
			'HOLGUIN': 'Holguín',
			'GRANMA': 'Granma',
			'SANTIAGO_DE_CUBA': 'Santiago de Cuba',
			'GUANTANAMO': 'Guantánamo',
			'ISLA_DE_LA_JUVENTUD': 'Isla de la Juventud'
		}
	},
	city: {caption: 'Ciudad', icon: 'place', values: {}},
	religion: {
		caption: 'Religión', icon: 'flare', values: {
			'ATEISMO': 'Ateísmo',
			'SECULARISMO': 'Secularismo',
			'AGNOSTICISMO': 'Agnosticismo',
			'ISLAM': 'Islamismo',
			'JUDAISTA': 'Judaísmo',
			'ABAKUA': 'Abakuá',
			'SANTERO': 'Santería',
			'YORUBA': 'Yorubismo',
			'BUDISMO': 'Budismo',
			'CATOLICISMO': 'Catolicismo',
			'CRISTIANISMO': 'Cristianismo',
			'PROTESTANTE': 'Protestante',
			'OTRA': 'Otra'
		}
	}
};

// On load

$(function () {
	$('.tabs').tabs();
	$('select').formSelect();
	$('.modal').modal();
	$('#about_me, #city').characterCounter();

	if (typeof profile != "undefined") {
		var interests = [];
		profile.interests.forEach(function (interest) {
			interests.push({tag: interest});
		});
		profile.interests = JSON.stringify(interests);
		$('.chips').chips();
		$('.chips-initial').chips({data: interests});

		var profileProps = $('#profile-props');
		if (profileProps.children().length == 0) profileProps.remove();
	}
});

// Requests functions

function openChat() {
	apretaste.send({
		command: 'chat',
		data: {
			id: profile.id
		}
	});
}

function pizarraSearch() {
	apretaste.send({
		command: 'pizarra',
		data: {
			id: profile.id
		}
	});
}

function openProfile() {
	if (typeof idPerson != "undefined") {
		apretaste.send({command: 'perfil', data: {id: idPerson}})
	} else {
		apretaste.send({command: 'perfil'})
	}
}

function openGallery() {
	if (typeof profile != "undefined") {
		apretaste.send({command: 'perfil imagenes', data: {id: profile.id}})
	} else {
		apretaste.send({command: 'perfil imagenes'})
	}
}

function openLevelsHelp() {
	apretaste.send({command: 'perfil niveles'});
}

function openEditProfile() {
	apretaste.send({command: 'perfil editar'});
}

// Functions

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

function getReadableProp(prop) {
	var readableProp = props[prop].values[profile[prop]];
	if (readableProp !== undefined) return readableProp;
	else return profile[prop];
}

function addFriendModalOpen() {
	M.Modal.getInstance($('#addFriendModal')).open();
}

var currentUser;

function setCurrentUsername(username) {
	$('.username').html('@' + username);
}

function acceptModalOpen(id, username) {
	currentUser = id;
	setCurrentUsername(username);
	M.Modal.getInstance($('#acceptFriendModal')).open();
}

function rejectModalOpen(id, username) {
	currentUser = id;
	setCurrentUsername(username);
	M.Modal.getInstance($('#rejectModal')).open();
}

function cancelRequestModalOpen(id, username) {
	currentUser = id;
	setCurrentUsername(username);
	M.Modal.getInstance($('#cancelRequestModal')).open();
}

function blockModalOpen(id, username) {
	currentUser = id;
	setCurrentUsername(username);
	M.Modal.getInstance($('#blockModal')).open();
}

function addFriend() {
	apretaste.send({
		command: 'amigos agregar',
		data: {id: profile.id},
		redirect: false,
		callback: {
			name: 'showToast',
			data: 'Solicitud enviada'
		}
	});
}

function uploadPicture() {
	loadFileToBase64();
}

function sendFile(base64File) {
	if (base64File.length > 2584000) {
		showToast("Imagen demasiado pesada");
		$('input:file').val(null);
		return false;
	}

	apretaste.send({
		"command": "PERFIL FOTO",
		"data": {'picture': base64File},
		"redirect": false,
		"callback": {
			"name": "updatePicture",
			"data": base64File
		}
	});
}

function showToast(text) {
	M.toast({html: text});
}

function deleteImage() {
	apretaste.send({
		'command': 'PERFIL BORRAR',
		'data': {'id': id}
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

function changeColor(color) {
	selectedColor = color;
	$('.mini-card .person-avatar').css('background-color', avatarColors[color]);
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
	/*if (profile.interests != JSON.stringify(M.Chips.getInstance($('.chips')).chipsData)) {
		data['interests'] = M.Chips.getInstance($('.chips')).chipsData;
	}*/

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
		command: "PERFIL UPDATE",
		data: data,
		redirect: false,
		callback: {
			name: 'onSaveCallback'
		}
	});

	// show success alert
	showToast("Sus cambios han sido guardados");
	return true;
}

// Callbacks

function updatePicture(file) {
	// add the picture to the gallery
	var imgElement =
		"<div class=\"col s6 m3 l2 galleryImage\"" +
		"	onclick=\"apretaste.send({'command': 'PERFIL VER', 'data': {'id': 'last'}})\">" +
		"	<img src=\"data:image/jpg;base64," + file + "\" class=\"responsive-img\" width=\"100%\"" +
		"	style=\"border-radius: 8px\">" +
		"</div>"

	if (images.length == 0) $('#gallery > div.col.s12').remove();
	$('#gallery').append(imgElement);

	showToast('Imagen agregada a la galeria');
}

function setAvatarCallback() {
	apretaste.send({
		'command': 'PERFIL'
	});
}

function onSaveCallback() {
	apretaste.send({
		'command': 'PERFIL', data: {'ts': Date.now()}
	});
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
