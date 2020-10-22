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
	country: {
		caption: 'País', icon: 'place', values: {
			"Afganistan": "AF",
			"Albania": "AL",
			"Alemania": "DE",
			"Andorra": "AD",
			"Angola": "AO",
			"Antigua y Barbuda": "AG",
			"Arabia Saudita": "SA",
			"Argelia": "DZ",
			"Argentina": "AR",
			"Armenia": "AM",
			"Australia": "AU",
			"Austria": "AT",
			"Azerbaiyan": "AZ",
			"Bahamas": "BS",
			"Bahrein": "BH",
			"Bangladesh": "BD",
			"Barbados": "BB",
			"Belarus": "BY",
			"Belgica": "BE",
			"Belice": "BZ",
			"Benin": "BJ",
			"Bhutan": "BT",
			"Bolivia": "BO",
			"Bosnia y Herzegovina": "BA",
			"Botswana": "BW",
			"Brasil": "BR",
			"Brunei Darussalam": "BN",
			"Bulgaria": "BG",
			"Burkina Faso": "BF",
			"Burundi": "BI",
			"Cabo Verde": "CV",
			"Camboya": "KH",
			"Camerun": "CM",
			"Canada": "CA",
			"Chad": "TD",
			"Chequia": "CZ",
			"Chile": "CL",
			"China": "CN",
			"Chipre": "CY",
			"Colombia": "CO",
			"Comoras": "KM",
			"Congo": "CG",
			"Costa de Marfil": "CI",
			"Costa Rica": "CR",
			"Croacia": "HR",
			"Cuba": "CU",
			"Dinamarca": "DK",
			"Djibouti": "DJ",
			"Dominica": "DM",
			"Ecuador": "EC",
			"Egipto": "EG",
			"El Salvador": "SV",
			"Emiratos Arabes Unidos": "AE",
			"Eritrea": "ER",
			"Eslovaquia": "SK",
			"Eslovenia": "SI",
			"Espana": "ES",
			"Estados Unidos": "US",
			"Estonia": "EE",
			"Etiopia": "ET",
			"ex República Yugoslava de Macedonia": "MK",
			"Federacion de Rusia": "RU",
			"Fiji": "FJ",
			"Filipinas": "PH",
			"Finlandia": "FI",
			"Francia": "FR",
			"Gabon": "GA",
			"Gambia": "GM",
			"Georgia": "GE",
			"Ghana": "GH",
			"Granada": "GD",
			"Grecia": "GR",
			"Guatemala": "GT",
			"Guinea": "GN",
			"Guinea Ecuatorial": "GQ",
			"Guinea-Bissau": "GW",
			"Guyana": "GY",
			"Haiti": "HT",
			"Honduras": "HN",
			"Hungria": "HU",
			"India": "IN",
			"Indonesia": "ID",
			"Iran": "IR",
			"Iraq": "IQ",
			"Irlanda": "IE",
			"Islandia": "IS",
			"Islas Cook": "CK",
			"Islas Marshall": "MH",
			"Islas Salomon": "SB",
			"Israel": "IL",
			"Italia": "IT",
			"Jamaica": "JM",
			"Japon": "JP",
			"Jordania": "JO",
			"Kazajstan": "KZ",
			"Kenya": "KE",
			"Kirguistan": "KG",
			"Kiribati": "KI",
			"Kuwait": "KW",
			"Lesotho": "LS",
			"Letonia": "LV",
			"Líbano": "LB",
			"Liberia": "LR",
			"Libia": "LY",
			"Lituania": "LT",
			"Luxemburgo": "LU",
			"Madagascar": "MG",
			"Malasia": "MY",
			"Malawi": "MW",
			"Maldivas": "MV",
			"Mali": "ML",
			"Malta": "MT",
			"Marruecos": "MA",
			"Mauricio": "MU",
			"Mauritania": "MR",
			"Mexico": "MX",
			"Micronesia": "FM",
			"Mónaco": "MC",
			"Mongolia": "MN",
			"Montenegro": "ME",
			"Mozambique": "MZ",
			"Myanmar": "MM",
			"Namibia": "NA",
			"Nauru": "NR",
			"Nepal": "NP",
			"Nicaragua": "NI",
			"Niger": "NE",
			"Nigeria": "NG",
			"Niue": "NU",
			"Noruega": "NO",
			"Nueva Zelandia": "NZ",
			"Oman": "OM",
			"Otro": "XX",
			"Paises Bajos": "NL",
			"Pakistan": "PK",
			"Palau": "PW",
			"Panama": "PA",
			"Papua Nueva Guinea": "PG",
			"Paraguay": "PY",
			"Peru": "PE",
			"Polonia": "PL",
			"Portugal": "PT",
			"Qatar": "QA",
			"Reino Unido": "GB",
			"República Arabe Siria": "SY",
			"República Centroafricana": "CF",
			"República de Corea": "KR",
			"República de Moldova": "MD",
			"República Democrática del Congo": "CD",
			"República Democrática Popular Lao": "LA",
			"República Dominicana": "DO",
			"República Popular Democrática de Corea": "KP",
			"República Unida de Tanzania": "TZ",
			"Rumania": "RO",
			"Rwanda": "RW",
			"Saint Kitts y Nevis": "KN",
			"Samoa": "WS",
			"San Marino": "SM",
			"San Vicente y las Granadinas": "VC",
			"Santa Lucía": "LC",
			"Santo Tome y Principe": "ST",
			"Senegal": "SN",
			"Serbia": "RS",
			"Seychelles": "SC",
			"Sierra Leona": "SL",
			"Singapur": "SG",
			"Somalia": "SO",
			"Sri Lanka": "LK",
			"Sudafrica": "ZA",
			"Sudan": "SD",
			"Sudan del Sur": "SS",
			"Suecia": "SE",
			"Suiza": "CH",
			"Suriname": "SR",
			"Swazilandia": "SZ",
			"Tailandia": "TH",
			"Tayikistan": "TJ",
			"Timor-Leste": "TL",
			"Togo": "TG",
			"Tonga": "TO",
			"Trinidad y Tabago": "TT",
			"Tunez": "TN",
			"Turkmenistan": "TM",
			"Turquia": "TR",
			"Tuvalu": "TV",
			"Ucrania": "UA",
			"Uganda": "UG",
			"Uruguay": "UY",
			"Uzbekistan": "UZ",
			"Vanuatu": "VU",
			"Venezuela": "VE",
			"Viet Nam": "VN",
			"Yemen": "YE",
			"Zambia": "ZM",
			"Zimbabwe": "ZW",
		}
	},
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
		resizeImg();
		showProvince();
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

// Main functions

function resizeImg() {
	if (typeof profile == "undefined") return;
	$('.profile-img').css('height', '');

	var img = $('#profile-avatar');
	var size = $(window).height() / 4; // picture must be 1/4 of the screen

	img.height(size);
	img.width(size);

	img.css('top', -4 - $(window).height() / 8 + 'px'); // align the picture with the div

	$('.profile-info').css('margin-top', 5 - $(window).height() / 6.5 + 'px'); // move the row before to the top to fill the empty space

	$('#img-pre').height(img.height() * 0.7); // set the height of the colored div after the photo
}

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
		command: 'pizarra global',
		data: {
			search: '@' + profile.username
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
			name: 'addFriendCallback'
		}
	});
}

function addFriendCallback() {
	showToast('Solicitud enviada');
	$('.actions').html(
		'<a onclick="pizarraSearch()" class="btn-floating waves-effect waves-light grey third">\n' +
		'                <i class="material-icons">assignment</i>\n' +
		'</a>\n' +
		'<a class="btn-floating waves-effect waves-light grey second" href="#!" onclick="apretaste.back()">\n' +
		'    <i class="material-icons">arrow_back</i>\n' +
		'</a>\n' +
		'<a class="btn-floating btn-large waves-effect waves-light">\n' +
		'    <i class="material-icons red-text"\n' +
		'       onclick="cancelRequestModalOpen(\'' + profile.id + '\', \'' + profile.username + '\')">\n' +
		'        delete\n' +
		'    </i>\n' +
		'</a>'
	);
}

function rejectFriend(message) {
	apretaste.send({
		command: 'amigos rechazar',
		data: {id: currentUser},
		redirect: false,
		callback: {
			name: 'rejectFriendCallback',
			data: message
		}
	});
}

function rejectFriendCallback(message) {
	showToast(message);

	$('.actions').html(
		'<a onclick="pizarraSearch()" class="btn-floating waves-effect waves-light grey third">\n' +
		'    <i class="material-icons">assignment</i>\n' +
		'</a>\n' +
		'<a class="btn-floating waves-effect waves-light grey second" href="#!" onclick="apretaste.back()">\n' +
		'    <i class="material-icons">arrow_back</i>\n' +
		'</a>\n' +
		'<a onclick="addFriendModalOpen()" class="btn-floating btn-large waves-effect waves-light">\n' +
		'    <i class="material-icons">person_add</i>\n' +
		'</a>'
	);
}

function deleteFriend() {
	apretaste.send({
		command: 'amigos eliminar',
		data: {id: currentUser},
		redirect: false,
		callback: {
			name: 'rejectFriendCallback',
			data: 'Amigo eliminado'
		}
	});
}

function uploadPicture() {
	loadFileToBase64();
}

function sendFile(base64File) {
	if (base64File.length > 2584000) {
		showToast("La imagen que escogió pesa mucho. Una solución rápida es tomar una captura de pantalla de la imagen, para disminuir el peso sin perder calidad.");
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
	$('#avatar-select .person-avatar').css('background-color', avatarColors[color]);
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

function showProvince() {
	var country = $('#country').val();
	var province = $('.province-div');

	switch (country) {
		case 'CU':
			province.show();
			break;

		default:
			province.hide();
			break;
	}
}

function validateUsername(event) {
	var valid = /[^a-zA-Z0-9@]+/.exec(event.key) == null;
	if (!valid) event.preventDefault();
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
	if (cleanUsername != profile.username && cleanUsername != '') data.username = cleanUsername;
	if (!isNaN(cleanUsername)) {
		showToast('El username debe contener al menos una letra');
		return;
	}

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
		"<div class=\"col s6 m4 galleryImage\"" +
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
		'command': 'PERFIL', data: {'ts': Date.now()}
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