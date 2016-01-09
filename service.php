<?php

class Perfil extends Service {
	/**
	 * Function called once this service is called
	 * 
	 * @param Request
	 * @return Response
	 * */
	public function _main(Request $request)
	{
		$request->query = trim($request->query);
		
		$email = $this->getEmailsFromRequest($request);
	 
		if (isset($email[0]))
		{
			if ($this->utils->personExist($email[0])) $request->query = $email[0];
			else $request->query = '';
		}

		// get the email for the profile if the user pass it through the query
		$emailToLookup = empty($request->query) ? $request->email : $request->query;

		// check if the person exist. If not, message the requestor
		if ( ! $this->utils->personExist($emailToLookup))
		{
			$responseContent = array("email" => $emailToLookup);
			$response = new Response();
			$response->setResponseSubject("No encontramos un perfil para ese usuario");
			$response->createFromTemplate("inexistent.tpl", $responseContent);
			return $response;
		}

		// get the full profile for the person
		$profile = $this->utils->getPerson($emailToLookup);

		// get the full name, or the email
		$fullName = empty($profile->full_name) ? $profile->email : $profile->full_name;

		// get the age
		$age = empty($profile->date_of_birth) ? "" : date_diff(date_create($profile->date_of_birth), date_create('today'))->y;

		// get the gender
		$gender = "";
		if ($profile->gender == "M")
			$gender = "hombre";
		if ($profile->gender == "F")
			$gender = "mujer";

		// get the final vowel based on the gender
		$genderFinalVowel = "o";
		if ($profile->gender == "F")
			$genderFinalVowel = "a";

		// get the eye color
		$eyes = "";
		if ($profile->eyes == "NEGRO")
			$eyes = "negro";
		if ($profile->eyes == "CARMELITA")
			$eyes = "carmelita";
		if ($profile->eyes == "AZUL")
			$eyes = "azul";
		if ($profile->eyes == "VERDE")
			$eyes = "verde";
		if ($profile->eyes == "AVELLANA")
			$eyes = "avellana";

		// get the eye tone
		$eyesTone = "";
		if ($profile->eyes == "NEGRO" || $profile->eyes == "CARMELITA" || $profile->eyes == "AVELLANA")
			$eyesTone = "oscuros";
		if ($profile->eyes == "AZUL" || $profile->eyes == "VERDE")
			$eyesTone = "claros";

		// get the skin color
		$skin = "";
		if ($profile->skin == "NEGRO")
			$skin = "negr$genderFinalVowel";
		if ($profile->skin == "BLANCO")
			$skin = "blanc$genderFinalVowel";
		if ($profile->skin == "MESTIZO")
			$skin = "mestiz$genderFinalVowel";

		// get the type of body
		$bodyType = "";
		if ($profile->body_type == "DELGADO")
			$bodyType = "soy flac$genderFinalVowel";
		if ($profile->body_type == "MEDIO")
			$bodyType = "no soy de flac$genderFinalVowel ni grues$genderFinalVowel";
		if ($profile->body_type == "EXTRA")
			$bodyType = "tengo unas libritas de m&aacute;s";
		if ($profile->body_type == "ATLETICO")
			$bodyType = "tengo un cuerpazo atl&eacute;tico";

		// get the hair color
		$hair = "";
		if ($profile->hair == "TRIGUENO")
			$hair = "trigue&ntilde;o";
		if ($profile->hair == "CASTANO")
			$hair = "casta&ntilde;o";
		if ($profile->hair == "RUBIO")
			$hair = "rubio";
		if ($profile->hair == "NEGRO")
			$hair = "negro";
		if ($profile->hair == "ROJO")
			$hair = "rojizo";
		if ($profile->hair == "BLANCO")
			$hair = "canoso";

		// get the place where the person live
		$province = "";
		if ($profile->province == "PINAR_DEL_RIO")
			$province = "Pinar del R&iacute;o";
		if ($profile->province == "LA_HABANA")
			$province = "La Habana";
		if ($profile->province == "ARTEMISA")
			$province = "Artemisa";
		if ($profile->province == "MAYABEQUE")
			$province = "Mayabeque";
		if ($profile->province == "MATANZAS")
			$province = "Matanzas";
		if ($profile->province == "VILLA_CLARA")
			$province = "Villa Clara";
		if ($profile->province == "CIENFUEGOS")
			$province = "Cienfuegos";
		if ($profile->province == "SANTI_SPIRITUS")
			$province = "Sancti Sp&iacute;ritus";
		if ($profile->province == "CIEGO_DE_AVILA")
			$province = "Ciego de &Aacute;vila";
		if ($profile->province == "CAMAGUEY")
			$province = "Camaguey";
		if ($profile->province == "LAS_TUNAS")
			$province = "Las Tunas";
		if ($profile->province == "HOLGUIN")
			$province = "Holgu&iacute;n";
		if ($profile->province == "GRANMA")
			$province = "Granma";
		if ($profile->province == "SANTIAGO_DE_CUBA")
			$province = "Santiago de Cuba";
		if ($profile->province == "GUANTANAMO")
			$province = "Guant&aacute;namo";
		if ($profile->province == "ISLA_DA_LA_JUVENTUD")
			$province = "Isla de la Juventud";

		// get the city
		$city = empty($profile->city) ? "" : ", {$profile->city}";

		// full location
		$location = ". Aunque prefiero no decir de donde soy";
		if (!empty($province))
			$location = ". Vivo en " . $province . $city;

		// get highest educational level
		$education = "";
		if ($profile->highest_school_level == "PRIMARIO")
			$education = "tengo sexto grado";
		if ($profile->highest_school_level == "SECUNDARIO")
			$education = "soy graduad$genderFinalVowel de la secundaria";
		if ($profile->highest_school_level == "TECNICO")
			$education = "soy t&acute;cnico medio";
		if ($profile->highest_school_level == "UNIVERSITARIO")
			$education = "soy universitari$genderFinalVowel";
		if ($profile->highest_school_level == "POSTGRADUADO")
			$education = "tengo estudios de postgrado";
		if ($profile->highest_school_level == "DOCTORADO")
			$education = "tengo un doctorado";

		// get marital status
		$maritalStatus = "";
		if ($profile->marital_status == "SOLTERO")
			$maritalStatus = "estoy solter$genderFinalVowel";
		if ($profile->marital_status == "SALIENDO")
			$maritalStatus = "estoy saliendo con alguien";
		if ($profile->marital_status == "COMPROMETIDO")
			$maritalStatus = "estoy comprometid$genderFinalVowel";
		if ($profile->marital_status == "CASADO")
			$maritalStatus = "soy casad$genderFinalVowel";

		// create the message
		$message = "Hola y bienvenido a mi perfil. Yo soy $fullName";
		if (!empty($age))
			$message .= ", tengo $age a&ntilde;os";
		if (!empty($gender))
			$message .= ", soy $gender";
		if (!empty($skin))
			$message .= ", soy $skin";
		if (!empty($eyes))
			$message .= ", de ojos $eyesTone (color $eyes)";
		if (!empty($eyes))
			$message .= ", soy de pelo $hair";
		if (!empty($bodyType))
			$message .= " y $bodyType";
		$message .= $location;
		if (!empty($education))
			$message .= ", $education";
		if (!empty($profile->occupation))
			$message .= ", trabajo como {$profile->occupation}";
		if (!empty($maritalStatus))
			$message .= " y $maritalStatus";
		$message .= ".";

		// create a json object to send to the template
		$responseContent = array(
			"profile" => $profile,
			"message" => $message,
			"editProfileText" => $this->utils->createProfileEditableText($profile->email),
			"ownProfile" => $emailToLookup == $request->email);

		// create the images to send to the response
		$di = \Phalcon\DI\FactoryDefault::getDefault();
		$wwwroot = $di->get('path')['root'];
		$image = empty($profile->thumbnail) ? array() : array($profile->thumbnail);

		// create a new Response object and input the template and the content
		$response = new Response();
		$response->setResponseSubject("Perfil de Apretaste");
		$response->createFromTemplate("profile.tpl", $responseContent, $image);
		return $response;
	}


	/**
	 * Function called when the profile is edited
	 *
	 * @param Request
	 * @return Response
	 * */
	public function _editar(Request $request)
	{
		// get the text to parse
		$email = $request->email;
		$body = $request->body;
		$attachments = $request->attachments;

/*
		$object = new stdClass();
		$object->path = '/var/www/Core/temp/test.jpg';
		$object->type = 'image/jpg';
		$attachments = array($object);
		$body="
		NOMBRE = Salvi Pascual
		CUMPLEANOS = 23/11/1985
		PROFESION = Programador
		PROVINCIA = havana
		CIUDAD = Miami-Dade
		SEXO = M
		NIVEL ESCOLAR = master
		ESTADO CIVIL = casado
		PELO = trigueno
		PIEL = blanca
		OJOS = verdes
		CUERPO = medio
		INTERESES = Networking, Amistad, Programacion, Apretaste, un trambia llamado deseo, una valiente jugada, porno pa ricardo, dimlo cantando, la politica no cabe en la azucarera
		";
*/
		// move the first image attached to the profiles directory
		$isImageAttached = 0;
		if (count($attachments) > 0)
		{
			$di = \Phalcon\DI\FactoryDefault::getDefault();
			$wwwroot = $di->get('path')['root'];

			foreach ($attachments as $attach)
			{
				if ($attach->type == "image/jpg")
				{
					// save the original copy
					$large = "$wwwroot/public/profile/$email.jpg";
					copy($attach->path, $large);
					$this->utils->optimizeImage($large);
					chmod($large, 0777);

					// create the thumbnail
					$thumbnail = "$wwwroot/public/profile/thumbnail/$email.jpg";
					copy($attach->path, $thumbnail);
					chmod($thumbnail, 0777);
					$this->utils->optimizeImage($thumbnail, 300);

					$isImageAttached = 1;
					break;
				}
			}
		}

		// rules to math the body
		$rules = array(
			array(
				"CUMPLEANOS",
				"date",
				null),
			array(
				"PROVINCIA",
				"enum",
				array(
					'PINAR_DEL_RIO',
					'LA_HABANA',
					'ARTEMISA',
					'MAYABEQUE',
					'MATANZAS',
					'VILLA_CLARA',
					'CIENFUEGOS',
					'SANTI_SPIRITUS',
					'CIEGO_DE_AVILA',
					'CAMAGUEY',
					'LAS_TUNAS',
					'HOLGUIN',
					'GRANMA',
					'SANTIAGO_DE_CUBA',
					'GUANTANAMO',
					'ISLA_DE_LA_JUVENTUD')),
			array(
				"SEXO",
				"gender",
				null),
			array(
				"NIVEL ESCOLAR",
				"enum",
				array(
					'PRIMARIO',
					'SECUNDARIO',
					'TECNICO',
					'UNIVERSITARIO',
					'POSTGRADUADO',
					'DOCTORADO',
					'OTRO')),
			array(
				"ESTADO CIVIL",
				"enum",
				array(
					'SOLTERO',
					'SALIENDO',
					'COMPROMETIDO',
					'CASADO')),
			array(
				"PELO",
				"enum",
				array(
					'TRIGUENO',
					'CASTANO',
					'RUBIO',
					'NEGRO',
					'ROJO',
					'BLANCO',
					'OTRO')),
			array(
				"PIEL",
				"enum",
				array(
					'NEGRO',
					'BLANCO',
					'MESTIZO',
					'OTRO')),
			array(
				"OJOS",
				"enum",
				array(
					'NEGRO',
					'CARMELITA',
					'VERDE',
					'AZUL',
					'AVELLANA',
					'OTRO')),
			array(
				"CUERPO",
				"enum",
				array(
					'DELGADO',
					'MEDIO',
					'EXTRA',
					'ATLETICO')),
			array(
				"INTERESES",
				"list",
				null));

		// parse the text
		$surveyParser = new SurveyParser();
		$res = $surveyParser->parse($body, $rules);

		// create the query and save new on the database
		$editedProfileValues = array();
		if (count($res) > 0) {
			// get the name
			$namePieces = null;
			if (!empty($res['NOMBRE'])) {
				$namePieces = $this->utils->fullNameToNamePieces($res['NOMBRE']);
			}

			// get the interests
			$interests = null;
			if (!empty($res['INTERESES'])) {
				$interests = implode(",", $res['INTERESES']);
			}

			// create query
			$query = "UPDATE person SET ";
			if ($namePieces)
				$query .= "
				first_name='{$namePieces[0]}', 
				middle_name='{$namePieces[1]}', 
				last_name='{$namePieces[2]}', 
				mother_name='{$namePieces[3]}',";
			if (!empty($res['CUMPLEANOS']))
				$query .= "date_of_birth='{$res['CUMPLEANOS']}',";
			if (!empty($res['SEXO']))
				$query .= "gender='{$res['SEXO']}',";
			if (!empty($res['OJOS']))
				$query .= "eyes='{$res['OJOS']}',";
			if (!empty($res['PIEL']))
				$query .= "skin='{$res['PIEL']}',";
			if (!empty($res['CUERPO']))
				$query .= "body_type='{$res['CUERPO']}',";
			if (!empty($res['PELO']))
				$query .= "hair='{$res['PELO']}',";
			if (!empty($res['PROVINCIA']))
				$query .= "province='{$res['PROVINCIA']}',";
			if (!empty($res['CIUDAD']))
				$query .= "city='{$res['CIUDAD']}',";
			if (!empty($res['NIVEL ESCOLAR']))
				$query .= "highest_school_level='{$res['NIVEL ESCOLAR']}',";
			if (!empty($res['PROFESION']))
				$query .= "occupation='{$res['PROFESION']}',";
			if (!empty($res['ESTADO CIVIL']))
				$query .= "marital_status='{$res['ESTADO CIVIL']}',";
			if (!empty($interests))
				$query .= "interests='$interests',";
			$query .= "
				last_update_date=CURRENT_TIMESTAMP, 
				updated_by_user=1, 
				picture=$isImageAttached 
			WHERE email='$email'";
			$query = preg_replace("/\s+/", " ", $query);

			// update in the database
			$connection = new Connection();
			$connection->deepQuery($query);

			// edit changed fields to go on the confirmation
			foreach ($res as $key => $value) {
				if (!empty($value)) {
					$valueToShow = $value;
					if ($key == "CUMPLEANOS")
						$valueToShow = strftime("%d de %B del %Y", strtotime($value));
					if ($key == "PROVINCIA")
						$valueToShow = str_replace("_", " ", $value);
					if ($key == "INTERESES")
						$valueToShow = implode(", ", $value);

					$editedProfileValues[$key] = $valueToShow;
				}
			}
		}

		// alert the user if the picture was udpatated
		if ($isImageAttached) {
			$editedProfileValues["IMAGE"] = "New image";
		}

		// get the full user profile
		$profile = $this->utils->getPerson($request->email);

		// create a json object to send to the template
		$responseContent = array(
			"editedProfileValues" => $editedProfileValues,
			"noProfileValuesWereEdited" => count($editedProfileValues) == 0,
			"editProfileText" => $this->utils->createProfileEditableText($profile->email),
			);

		// send response to the user
		$response = new Response();
		$response->setResponseSubject("Su perfil ha sido editado");
		$response->createFromTemplate("confirmation.tpl", $responseContent);
		return $response;
	}

	/**
	 * Return a list of emails from request query
	 * 
	 * @param Request $request
	 * @return array
	 */
	private function getEmailsFromRequest($request) {

		$db = new Connection();

		$query = explode(" ", $request->query);

		$parts = array();

		foreach ($query as $q) {
			$part = trim($q);
			if ($part !== '')
				$parts[] = $part;
		}

		$emails = array();
		foreach ($parts as $part) {
			$find = $db->deepQuery("SELECT email FROM person WHERE username = '{$part}';");

			if (isset($find[0])) {
				$emails[] = $find[0]->email;
			} else
				$emails[] = $part;
		}

		return $emails;
	}
}
