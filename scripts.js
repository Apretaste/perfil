$(document).ready(() => {
  //For main profile
  $('.materialboxed').materialbox();
  if (typeof profile != "undefined" && typeof ownProfile != "undefined") {
    if (ownProfile) {
      $("#editar").click(function () {
        return apretaste.send({"command": "PERFIL EDITAR"})
      });
      $("#credito").click(function () {
        apretaste.send({"command": 'CREDITO'});
      });
      $("#rifa").click(function () {
        apretaste.send({"command": 'RIFA'});
      });
    }
    else {
     /* $("#chat").click(function () {
        apretaste.send({
          "command": 'CHAT',
          data: {"username": profile.username}
        });
      });
*/
      $("#bloquear").click(function () {
        apretaste.send({
          "command": 'PERFIL BLOQUEAR',
          data: {"username": profile.username}
        });
      });
    }
  }

  //For origin
  if (typeof origin != "undefined") {
    $('select').formSelect();
    $('#origin').change((event) => {
      let selected = $('#origin option:selected').val();
      return apretaste.send({
        "command": "PERFIL UPDATE",
        "data": {"origin": selected},
        "redirect": false,
        "callback": (!$('#picture').length) ? {
          "name": "reloadOrigin",
          "data": selected
        } : false
      });
    });

    $('#origin option[value="' + origin + '"]').prop("selected", true);
  }

  //For profile edit
  if ($('#picture').length) {
    $('#uploadPhoto').click((e) => loadFileToBase64());

    let provinces = [
      'Pinar del Rio', 'La Habana', 'Artemisa', 'Mayabeque',
      'Matanzas', 'Villa Clara', 'Cienfuegos', 'Sancti Spiritus',
      'Ciego de Avila', 'Camaguey', 'Las Tunas', 'Holguin',
      'Granma', 'Santiago de Cuba', 'Guantanamo', 'Isla de la Juventud', 'Otro'
    ];

    let states = [
      {caption: 'Alabama', value: 'AL'},
      {caption: 'Alaska', value: 'AK'},
      {caption: 'Arizona', value: 'AZ'},
      {caption: 'Arkansas', value: 'AR'},
      {caption: 'California', value: 'CA'},
      {caption: 'Carolina del Norte', value: 'NC'},
      {caption: 'Carolina del Sur', value: 'SC'},
      {caption: 'Colorado', value: 'CO'},
      {caption: 'Connecticut', value: 'CT'},
      {caption: 'Dakota del Norte', value: 'ND'},
      {caption: 'Dakota del Sur', value: 'SD'},
      {caption: 'Delaware', value: 'DE'},
      {caption: 'Florida', value: 'FL'},
      {caption: 'Georgia', value: 'GA'},
      {caption: 'Hawái', value: 'HI'},
      {caption: 'Idaho', value: 'ID'},
      {caption: 'Illinois', value: 'IL'},
      {caption: 'Indiana', value: 'IN'},
      {caption: 'Iowa', value: 'IA'},
      {caption: 'Kansas', value: 'KS'},
      {caption: 'Kentucky', value: 'KY'},
      {caption: 'Luisiana', value: 'LA'},
      {caption: 'Maine', value: 'ME'},
      {caption: 'Maryland', value: 'MD'},
      {caption: 'Massachusetts', value: 'MA'},
      {caption: 'Míchigan', value: 'MI'},
      {caption: 'Minnesota', value: 'MN'},
      {caption: 'Misisipi', value: 'MS'},
      {caption: 'Misuri', value: 'MO'},
      {caption: 'Montana', value: 'MT'},
      {caption: 'Nebraska', value: 'NE'},
      {caption: 'Nevada', value: 'NV'},
      {caption: 'Nueva Jersey', value: 'NJ'},
      {caption: 'Nueva York', value: 'NY'},
      {caption: 'Nuevo Hampshire', value: 'NH'},
      {caption: 'Nuevo México', value: 'NM'},
      {caption: 'Ohio', value: 'OH'},
      {caption: 'Oklahoma', value: 'OK'},
      {caption: 'Oregón', value: 'OR'},
      {caption: 'Pensilvania', value: 'PA'},
      {caption: 'Rhode Island', value: 'RI'},
      {caption: 'Tennessee', value: 'TN'},
      {caption: 'Texas', value: 'TX'},
      {caption: 'Utah', value: 'UT'},
      {caption: 'Vermont', value: 'VT'},
      {caption: 'Virginia', value: 'VA'},
      {caption: 'Virginia Occidental', value: 'WV'},
      {caption: 'Washington', value: 'WA'},
      {caption: 'Wisconsin', value: 'WI'},
      {caption: 'Wyoming', value: 'WY'}
    ];

    provinces.forEach((province) => {
      $('#province').prepend('<option value=\'' + province.toUpperCase() + '\'>' + province + '</option>');
    });

    $('#gender option[value="' + profile.gender + '"]').prop("selected", true);
    $('#orientation option[value="' + profile.sexual_orientation + '"]').prop("selected", true);
    $('#marital_status option[value="' + profile.marital_status + '"]').prop("selected", true);
    $('#religion option[value="' + profile.religion + '"]').prop("selected", true);
    $('#country option[value="' + profile.country + '"]').prop("selected", true);
    $('#province option[value="' + profile.province + '"]').prop("selected", true);
    $('#body_type option[value="' + profile.body_type + '"]').prop("selected", true);
    $('#eyes option[value="' + profile.eyes + '"]').prop("selected", true);
    $('#skin option[value="' + profile.skin + '"]').prop("selected", true);
    $('#hair option[value="' + profile.hair + '"]').prop("selected", true);
    $('#highest_school_level option[value="' + profile.highest_school_level + '"]').prop("selected", true);
    $('#occupation option[value="' + profile.occupation + '"]').prop("selected", true);

    $('#country').on('change', function () { // Important! Do not use lambda notation
      $('#province').html('');
      if ($(this).val() == 'US') {
        states.forEach((state) => {
          $('#province').prepend('<option value=\'' + state.value + '\'>' + state.caption + '</option>');
        });
      }
      else {
        provinces.forEach((province) => {
          $('#province').prepend('<option value=\'' + province.toUpperCase() + '\'>' + province + '</option>');
        });
      }
      $('select').formSelect();
    });

    $('select').formSelect();

    var date = new Date();
    var today = '12/31/' + date.getFullYear();

    $('.datepicker').datepicker({
      format: 'd/mm/yyyy',
      defaultDate: new Date(profile.date_of_birth),
      setDefaultDate: true,
      selectMonths: true, // Creates a dropdown to control month
      selectYears: 15, // Creates a dropdown of 15 years to control year,
      max: true,
      today: 'Hoy',
      clear: 'Limpiar',
      close: 'Aceptar'
    });

    profile.date_of_birth = $('#date_of_birth').val();
    let interests = [];
    profile.interests.forEach((interest) => {
      interests.push({tag: interest});
    });
    profile.interests = JSON.stringify(interests);

    $('.chips').chips();
    $('.chips-initial').chips({
      data: interests,
    });

    $('.save').click(() => {
      var names = [
        'first_name', 'last_name', 'username', 'date_of_birth', 'country', 'province',
        'city', 'gender', 'sexual_orientation', 'marital_status', 'religion', 'body_type',
        'eyes', 'skin', 'hair', 'highest_school_level', 'occupation', 'origin'
      ];
      $('#username').val($('#username').val().replace('@', ''));
      var data = new Object;
      names.forEach((prop) => {
        if ($('#' + prop).val() != profile[prop] && $('#' + prop).val() != null) {
          data[prop] = $('#' + prop).val();
        }
      });
      if (profile.interests != JSON.stringify(M.Chips.getInstance($('.chips')).chipsData)) {
        data['interests'] = M.Chips.getInstance($('.chips')).chipsData;
      }
      $('#username').val('@' + $('#username').val());

      if (!$.isEmptyObject(data)) {
        return apretaste.send({
          "command": "PERFIL UPDATE",
          "data": data,
          "redirect": false,
          "callback": {
            "name": "showToast",
            "data": "Sus cambios han sido guardados"
          }
        });
      }
      else {
        showToast("Usted no ha hecho ningun cambio");
      }
    });
  }
});

function reloadOrigin(origin) {
  $("#actual").html(origin);
  showToast('Respuesta guardada');
}

function showToast(text) {
  M.toast({html: text});
}

function updatePicture(file) {
  // display the picture on the img
  $('#pic').attr('src', "data:image/jpg;base64," + file);

  // show confirmation text
  showToast('Su foto ha sido cambiada correctamente');
}

function sendFile(base64File) {
  apretaste.send({
    "command": "PERFIL FOTO",
    "data": {'picture': base64File},
    "redirect": false,
    "callback": {"name": "updatePicture", "data": base64File}
  });
}