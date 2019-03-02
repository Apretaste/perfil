$(document).ready(() => {
	//For main profile
	$('.materialboxed').materialbox();
	if (typeof profile!="undefined" && typeof ownProfile!="undefined") {
		if(ownProfile){
			$("#editar").click(function() {
				return apretaste.send({"command": "PERFIL EDITAR"})
			});
			$("#credito").click(function() {
				apretaste.send({"command": 'CREDITO'});
			});
			$("#rifa").click(function() {
				apretaste.send({"command": 'RIFA'});
			});
		}else{
			$("#chat").click(function() {
				apretaste.send({"command": 'CHAT', data: {"username":profile.username}});
			});

			$("#bloquear").click(function() {
				apretaste.send({"command": 'PERFIL BLOQUEAR', data: {"username":profile.username}});
			});
		}
	}

	//For origin
	if(typeof origin!="undefined"){
		$('select').formSelect();
		$('#origin').change((event) =>{
			let selected = $('#origin option:selected').val();
			return apretaste.send({
				"command":"PERFIL UPDATE",
				"data":{"origin":selected},
				"redirect":false,
				"callback":(!$('#picture').length)?{"name":"reloadOrigin","data":selected}:false
			});
		});

		$('#origin option[value="'+origin+'"]').prop("selected", true);
	}

	//For profile edit
	if($('#picture').length){
		$('#uploadPhoto').click((e) => loadFileToBase64());

		let provinces = [
			'Pinar del Rio', 'La Habana', 'Artemisa', 'Mayabeque',
			'Matanzas', 'Villa Clara', 'Cienfuegos', 'Sancti Spiritus',
			'Ciego de Avila', 'Camaguey', 'Las Tunas', 'Holguin',
			'Granma', 'Santiago de Cuba', 'Guantanamo', 'Isla de la Juventud','Otro'
		];
		
		provinces.forEach((province) => {
			$('#province').prepend('<option value=\''+province.toUpperCase()+'\'>'+province+'</option>');
		});

		$('#gender option[value="'+profile.gender+'"]').prop("selected", true);
		$('#orientation option[value="'+profile.sexual_orientation+'"]').prop("selected", true);
		$('#marital_status option[value="'+profile.marital_status+'"]').prop("selected", true);
		$('#religion option[value="'+profile.religion+'"]').prop("selected", true);
		$('#country option[value="'+profile.country+'"]').prop("selected", true);
		$('#province option[value="'+profile.province+'"]').prop("selected", true);
		$('#body_type option[value="'+profile.body_type+'"]').prop("selected", true);
		$('#eyes option[value="'+profile.eyes+'"]').prop("selected", true);
		$('#skin option[value="'+profile.skin+'"]').prop("selected", true);
		$('#hair option[value="'+profile.hair+'"]').prop("selected", true);
		$('#highest_school_level option[value="'+profile.highest_school_level+'"]').prop("selected", true);
		$('#occupation option[value="'+profile.occupation+'"]').prop("selected", true);
		
		$('select').formSelect();
		$('.datepicker').datepicker({
			format: 'd/mm/yyyy',
			defaultDate: new Date(profile.date_of_birth),
			setDefaultDate: true
		});

		profile.date_of_birth = $('#date_of_birth').val();
		let interests = [];
		profile.interests.forEach((interest) => {
			interests.push({tag:interest});
		});
		profile.interests =  JSON.stringify(interests);

		$('.chips').chips();
		$('.chips-initial').chips({
			data: interests,
		  });
		
		$('.save').click(() =>{
			var names = [
				'first_name','last_name','username','date_of_birth','country','province',
				'city','gender','sexual_orientation','marital_status','religion','body_type',
				'eyes','skin','hair','highest_school_level','occupation','origin'
			];
			$('#username').val($('#username').val().replace('@',''));
			var data = new Object;
			names.forEach((prop) =>{
				if($('#'+prop).val() != profile[prop] && $('#'+prop).val()!=null) data[prop] =  $('#'+prop).val();
			});
			if(profile.interests != JSON.stringify(M.Chips.getInstance($('.chips')).chipsData)){
				data['interests'] = M.Chips.getInstance($('.chips')).chipsData;
			}
			$('#username').val('@'+$('#username').val());

			if(!$.isEmptyObject(data)){
				return apretaste.send({
					"command":"PERFIL UPDATE",
					"data":data,
					"redirect":false,
					"callback":{"name":"showToast","data":"Sus cambios han sido guardados"}
					});
			}
			else showToast("Usted no ha hecho ningun cambio");
		});
	}
});

function reloadOrigin(origin){
	$("#actual").html(origin);
	showToast('Respuesta guardada');
}

function showToast(text){
	M.toast({html: text});
}

function updatePicture(file){
    // display the picture on the img
    $('#pic').attr('src', "data:image/jpg;base64,"+file);

    // show confirmation text
    showToast('Su foto ha sido cambiada correctamente');
}

function sendFile(base64File){
    apretaste.send({
        "command":"PERFIL FOTO",
        "data":{'picture':base64File},
        "redirect":false,
        "callback":{"name":"updatePicture","data":base64File}
    });
}