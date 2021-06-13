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

// list of social links
var socialLinks = [
	{
		name: 'facebook',
		icon: 'fab fa-facebook-f',
		text: 'Escriba su identificador de perfil en Facebook. Aparece en la barra de búsqueda cuando abre su muro desde el navegador, y puede que sea un número.',
		caption: 'facebook.com/',
		link: 'https://www.facebook.com/',
	}, {
		name: 'twitter',
		icon: 'fab fa-twitter',
		text: 'Escriba su @username en Twitter.',
		caption: 'twitter.com/',
		link: 'https://twitter.com/',
	}, {
		name: 'instagram',
		icon: 'fab fa-instagram',
		text: 'Escriba su nombre de usuario en Instagram.',
		caption: 'instagram.com/',
		link: 'https://instagram.com/',
	}, {
		name: 'telegram',
		icon: 'fab fa-telegram-plane',
		text: 'Escriba su @username en Telegram, no su teléfono. Si no tiene un @username, créeselo antes.',
		caption: '@',
		link: 'https://t.me/',
	}, {
		name: 'whatsapp',
		icon: 'fab fa-whatsapp',
		text: 'Escriba el número de teléfono que usa en whatsapp, comenzando por el prefijo del país. Este número será público y otros podrán verlo. No agregue su número si desea mantener su anonimato.',
		caption: '+',
		link: 'https://api.whatsapp.com/send?phone=',
	}, {
		name: 'website',
		icon: 'fas fa-globe',
		text: 'Agregue su website, comenzando por http.',
		caption: '',
		link: '',
	}
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
	$('.fixed-action-btn').floatingActionButton({direction: 'up', hoverEnabled: false});

	if (typeof profile != "undefined") {
		resizeImg();
		showProvince();

		if (typeof profile.interests != "undefined") {
			var interests = [];
			profile.interests.forEach(function (interest) {
				interests.push({tag: interest});
			});
			profile.interests = JSON.stringify(interests);
			$('.chips').chips();
			$('.chips-initial').chips({data: interests});
		}

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

	var initialMargin = 5;
	if (profile.isInfluencer) initialMargin += 10;
	$('.profile-info').css('margin-top', initialMargin - $(window).height() / 6.5 + 'px'); // move the row before to the top to fill the empty space

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
		command: 'pizarra popular',
		data: {
			search: '@' + profile.username
		}
	});
}

function openProfile() {
	if (typeof profile.id != "undefined") {
		apretaste.send({command: 'perfil', data: {id: profile.id}})
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

function openFriendsList() {
	apretaste.send({command: 'amigos'});
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

function shareProfile() {
	apretaste.share({
		title: 'Mira el perfil de @' + profile.username + ' en Apretaste',
		link: 'http://apretaste.me/profile/' + profile.username
	});
}

function openDonationModal() {
	M.Modal.getInstance($('#donationModal')).open();
}

function addFriendModalOpen() {
	M.Modal.getInstance($('#addFriendModal')).open();
}

function acceptModalOpen() {
	M.Modal.getInstance($('#acceptFriendModal')).open();
}

function rejectModalOpen() {
	M.Modal.getInstance($('#rejectModal')).open();
}

function cancelRequestModalOpen() {
	M.Modal.getInstance($('#cancelRequestModal')).open();
}

function blockModalOpen() {
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

function addFriendCallback(data) {
	showToast('Solicitud enviada');
	apretaste.send({command: 'perfil', data: {id: profile.id}});
}

function rejectFriend(message) {
	apretaste.send({
		command: 'amigos rechazar',
		data: {id: profile.id},
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

function deleteModalOpen() {
	M.Modal.getInstance($('#deleteModal')).open();
}

function deleteFriend() {
	apretaste.send({
		command: 'amigos eliminar',
		data: {id: profile.id},
		redirect: false,
		callback: {
			name: 'rejectFriendCallback',
			data: 'Amigo eliminado'
		}
	});
}

function uploadPicture() {
	if (typeof apretaste.loadImage != 'undefined') {
		apretaste.loadImage('onImageLoaded')
	} else {
		loadFileToBase64();
	}
}

function onImageLoaded(path) {
	var basename = path.split(/[\\/]/).pop()
	apretaste.send({
		"command": "PERFIL FOTO",
		"data": {'pictureName': basename},
		"redirect": false,
		"files": [path],
		"callback": {
			"name": "showUploadedPicture",
			"data": path
		}
	});
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

function sendDonation() {
	var amount = parseFloat($('#donationAmount').val());
	if (amount > parseFloat(myCredit)) {
		showToast('No tienes suficiente crédito');
		return;
	} else if (amount < 0.1) {
		showToast('El minimo a donar es §0.1');
		return;
	}

	apretaste.send({
		command: 'perfil donar',
		data: {'amount': amount, influencer: profile.id}
	});
}

function showToast(text) {
	M.toast({html: text});
}

function deleteImage(id, e) {
	// confirm deletion
	var areYouSure = confirm('¿Está seguro de eliminar esta imagen?');
	if (!areYouSure) return false;

	// change in the backend
	apretaste.send({
		command: 'PERFIL BORRAR',
		data: {id: id}
	});

	// delete element from the view
	$(e).parents('.galleryImage').remove();
}

function selectDefaultImage(id, e) {
	// change in the backend
	apretaste.send({
		command: 'PERFIL FOTO',
		data: {id: id},
		redirect: false
	});

	// change star
	$('.fa-star').removeClass('yellow-text');
	$(e).find('.fa-star').addClass('yellow-text');
	$(e).parents('.galleryImage').find('.trash').remove();

	// show toast
	showToast('Imagen principal cambiada');
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
	var username = $('#username');

	if (username.substr(-3).toLowerCase() === 'bot') {
		showToast('El nombre de usuario no debe contener terminar en la palabra "bot"');
		return false;
	}

	var value = username.val();
	var valid = /[^a-zA-Z0-9@]+/.exec(value);

	while (valid != null) {
		value = value.replace(valid[0], '');
		valid = /[^a-zA-Z0-9@]+/.exec(value);
	}
	username.val(value);
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

	if (cleanUsername.substr(-3).toLowerCase() === 'bot') {
		showToast('El nombre de usuario no debe contener terminar en la palabra "bot"');
		return false;
	}

	if (cleanUsername != profile.username && cleanUsername != '') data.username = cleanUsername;
	if (!isNaN(cleanUsername)) {
		showToast('El nombre de usuario debe contener al menos una letra');
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
	file = "data:image/jpg;base64," + file;
	showUploadedPicture(file)
}

function showUploadedPicture(file) {
	// add the picture to the gallery
	var imgElement =
		"<div class=\"col s6 m4 galleryImage\"" +
		"	onclick=\"apretaste.send({'command': 'PERFIL VER', 'data': {'id': 'last'}})\">" +
		"	<img src=\"" + file + "\" class=\"responsive-img\" width=\"100%\"" +
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

// open the social modal to update
function updateSocialOpen(type) {
	// select type info
	var social = getSocialLink(type);

	// update labels
	$('#updateSocialModal .social-icon').removeClass('fab fas fa-facebook-f fa-twitter fa-instagram fa-telegram-plane fa-whatsapp fa-globe').addClass(social.icon);
	$('#updateSocialModal .social-text').html(social.text);
	$('#social').attr('data-type', type);

	// open the modal
	M.Modal.getInstance($('#updateSocialModal')).open();

	// focus on the social input
	$('#social').focus();
}

// submit the social modal
function updateSocial() {
	// get social values
	var type = $('#social').attr('data-type');
	var value = $('#social').val().trim();

	// do not let pass values with spaces
	if (/\s/.test(value)) {
		showToast('Su valor parece inválido');
		return false;
	}

	// validate websites
	if (type == 'website') {
		if (!isValidURL(value)) {
			showToast('Su website es inválida');
			return false;
		}
	}

	// clean special chars if no website
	if (type != 'website') {
		value = value.replace(/[^\w\s]/gi, '');
	}

	// change the link on the component
	if (value.length > 0) {
		var social = getSocialLink(type);
		$('.' + type + '-link').html(social.caption + value).removeClass('grey-text').addClass('green-text');
	} else {
		$('.' + type + '-link').html('Agregar cuenta').removeClass('green-text').addClass('grey-text');
	}

	// clean the input
	$('#social').val('');

	// save the link
	apretaste.send({
		"command": "PERFIL UPDATE",
		"data": JSON.parse('{"' + type + '":"' + value + '"}'),
		"showLoading": false,
		"redirect": false
	});

	// display confirmation
	showToast('Vínculo social guardado');
}

// get a social link from the list
function getSocialLink(name) {
	for (var i = socialLinks.length - 1; i >= 0; i--) {
		if (socialLinks[i].name == name) return socialLinks[i];
	}
}

// check if an URL is valid
function isValidURL(str) {
	var pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
		'((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
		'((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
		'(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
		'(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
		'(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
	return !!pattern.test(str);
}

function blockUser(id) {
	apretaste.send({
		command: 'amigos bloquear',
		data: {id: id},
		redirect: false,
		callback: {
			name: 'blockUserCallback',
			data: id
		}
	});
}

function blockUserCallback(id) {
	apretaste.send({command: 'AMIGOS BLOCKED', useCache: false});
}


function remainder(size) {
	if (size == null) {
		size = 250;
	}
	// get message and remainder amount
	var comment = $('#comment').val().trim();
	var remainder = (comment.length <= size) ? (size - comment.length) : 0;

	// restrict comment size
	if (remainder <= 0) {
		comment = comment.substring(0, size);
		$('#comment').val(comment);
	}

	// update remainder amount
	$('#remainder').html(comment.length);
}


function hideKeyboard() {
	if (
		document.activeElement &&
		document.activeElement.blur &&
		typeof document.activeElement.blur === 'function'
	) {
		document.activeElement.blur()
	}
}

let currentImage = null;
function sendImageToChat(id){
	currentImage = id;
	var instance = M.Modal.getInstance($('#newCommentModal'));
	instance.open();
	// $('#comment').show();
}

function sendComment() {

	hideKeyboard();
	var message = $('#comment').val().trim();

	// if (comment.length >= 2) {
		apretaste.send({
			command: 'CHAT PERFILIMAGE',
			data: {
				message: message,
				image: currentImage
			},
			redirect: false,
			async: true,
			callback: {name: 'sendCommentAsyncCallback'}
		});

		sendCommentCallback(comment.escapeHTML());
/*	} else {
		showToast('Escriba algo');
	}*/
}

function sendCommentCallback(comment) {
	$('#comment').val('');
	showToast('Mensaje enviado');
}