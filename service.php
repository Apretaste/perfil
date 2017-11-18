<?php

class Perfil extends Service
{
	/**
	 * Display your profile
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _main (Request $request)
	{
		// get the email or the username for the profile
		$request->query = trim($request->query, "@ ");
		$emailToLookup = empty($request->query) ? $request->email : $request->query;

		// get the email for the profile in case it is a username
		if ( ! filter_var($emailToLookup, FILTER_VALIDATE_EMAIL))
		{
			$emailToLookup = $this->utils->getEmailFromUsername($emailToLookup);
		}

		// check if the person exist. If not, message the requestor
		if ( ! $this->utils->personExist($emailToLookup))
		{
			$response = new Response();
			$response->setResponseSubject("No encontramos un perfil para ese usuario");
			$response->createFromTemplate("inexistent.tpl", array("code"=>"error", "user"=>$emailToLookup));
			return $response;
		}

		// get the person
		$connection = new Connection();
		$person = $connection->query("SELECT * FROM person WHERE email = '$emailToLookup'");

		// prepare the full profile
		$social = new Social();
		$profile = $social->prepareUserProfile($person[0], $request->lang);

		// check if current user follow the user to lookup
		$profile->follow = false;
		$sql = "SELECT COUNT(user1) as total FROM relations WHERE user1 = '{$request->email}' AND user2 = '$emailToLookup' AND type = 'follow';";
		$r = $connection->query($sql);
		if ($r[0]->total * 1 > 0) $profile->follow = true;

		// get the number of tickets for the raffle
		$tickets = $connection->query("SELECT count(ticket_id) as tickets FROM ticket WHERE raffle_id is NULL AND email = '$emailToLookup'");

		// pass variables to the template
		$responseContent = array(
			"profile" => $profile,
			"tickets" => $tickets[0]->tickets,
			"ownProfile" => $emailToLookup == $request->email
		);

		// pass profile image to the response
		$image = $profile->picture ? array($profile->picture_internal) : array();

		// create a new Response object and input the template and the content
		$response = new Response();
		if($request->query) $response->setCache("day");
		$response->setResponseSubject("Perfil de Apretaste");
		$response->createFromTemplate("profile.tpl", $responseContent, $image);
		return $response;
	}

	/**
	 * Subservice USERNAME
	 *
	 * @param Request $request
	 */
	public function _username (Request $request)
	{
		// clean, shorten and lowercase the text
		$username = preg_replace("/[^a-zA-Z0-9]+/", "", $request->query);
		$username = strtolower(substr($username, 0, 15));

		// check if the username exist, else recreate it
		$connection = new Connection();
		$exist = $connection->query("SELECT COUNT(email) AS exist FROM person WHERE username='$username'");
		if($exist[0]->exist) $username = substr($username, 0, 10) . rand(11111, 99999);

		// update the username in the database
		$this->update(" username='$username'", $request->email);
		return new Response();
	}

	/**
	 * Subservice NOMBRE
	 *
	 * @param Request $request
	 */
	public function _nombre (Request $request)
	{
		// get the pieces of names
		$n = $this->utils->fullNameToNamePieces(trim($request->query));
		if ( ! is_array($n)) return new Response();

		// create the query
		$query = " first_name='{$n[0]}', middle_name='{$n[1]}', last_name='{$n[2]}', mother_name='{$n[3]}'";

		// update the name in the database
		$this->update($query, $request->email);
		return new Response();
	}

	/**
	 * Subservice PHONE
	 *
	 * @param Request $request
	 */
	public function _phone (Request $request)
	{
		// remove all non-numeric characters from the phone
		$phone = preg_replace('/[^0-9.]+/', '', $request->query);

		// update the phone in the database
		$isCell = substr($phone, 0, 1) == 5;
		if($isCell) $this->update(" cellphone='$phone'", $request->email);
		else $this->update(" phone='$phone'", $request->email);

		return new Response();
	}

	/**
	 * Subservice TELEFONO
	 *
	 * @param Request $request
	 */
	public function _telefono (Request $request)
	{
		// get only numbers from string
		$phone = preg_replace('/\D/', '', $request->query);

		// save phone to the database
		if($phone) {
			$sql = "phone = '$phone'";
			$this->update($sql, $request->email);
		}

		// do not send emails back
		return new Response();
	}

	/**
	 * Subservice CUMPLEANOS
	 *
	 * @param Request $request
	 */
	public function _cumpleanos (Request $request)
	{
		return $this->subserviceDate($request, 'date_of_birth');
	}

	/**
	 * Subservice PROFESION
	 *
	 * @param Request $request
	 */
	public function _profesion (Request $request)
	{
		return $this->subserviceSimple($request, 'occupation');
	}

	/**
	 * Subservice RELIGION
	 *
	 * @param Request $request
	 */
	public function _religion (Request $request)
	{
		$religions = array(
			'ATEISMO',
			'SECULARISMO',
			'AGNOSTICISMO',
			'ISLAM',
			'JUDAISTA',
			'ABAKUA',
			'SANTERO',
			'YORUBA',
			'BUDISMO',
			'CATOLICISMO',
			'OTRA',
			'CRISTIANISMO'
		);

		$synon = array(
			'ATEO' => 'ATEISMO',
			'SECULAR' => 'SECULARISMO',
			'AGNOSTICO' => 'AGNOSTICISMO',
			'CATOLICO' => 'CATOLICISMO',
			'CRISTIANO' => 'CRISTIANISMO',
			'BUDISTA' => 'BUDISMO'
		);

		return $this->subserviceEnum($request, 'religion', $religions, 'Dinos tu religion o si careces de ella', null, $synon);
	}

	/**
	 * Subservice PROVINCIA
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _provincia (Request $request)
	{
		// get the province codes
		$provs = array(
			'PINAR_DEL_RIO', 'LA_HABANA', 'ARTEMISA', 'MAYABEQUE',
			'MATANZAS', 'VILLA_CLARA', 'CIENFUEGOS', 'SANCTI_SPIRITUS',
			'CIEGO_DE_AVILA', 'CAMAGUEY', 'LAS_TUNAS', 'HOLGUIN',
			'GRANMA', 'SANTIAGO_DE_CUBA', 'GUANTANAMO', 'ISLA_DE_LA_JUVENTUD'
		);

		// create the province names
		$synon = array();
		foreach ($provs as $v) $synon[str_replace('_', ' ', $v)] = $v;

		// save country as Cuba
		$this->update("country='CU', usstate=NULL", $request->email);

		// update the province
		return $this->subserviceEnum($request, 'province', $provs, 'Diga la provincia donde vive', null, $synon);
	}

	/**
	 * Subservice PAIS
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _pais (Request $request)
	{
		// get the list of countries
		$connection = new Connection();
		$countries = $connection->query("SELECT code, es AS name FROM countries ORDER BY code");
		$country = trim($request->query);
		$country_original = $country;

		// do not let empty countries
		if (empty($country))
		{
			$response = new Response();
			$response->setResponseSubject("Selecciona el pais donde vive");
			$response->createFromTemplate("profile_edit_country.tpl", array('countries' => $countries));
			return $response;
		}

		// setup country aliases and typos
		if($country == "US") $country = "Estados Unidos de America";
		if($country == "USA") $country = "Estados Unidos de America";
		if($country == "estados unidos") $country = "Estados Unidos de America";
		if($country == "kuba") $country = "cu";
		if($country == "usa") $country = "us";
		if($country == "estados unidos de america") $country = "us";

		// get the country to update
		$max = 0;

		$l_country = strtolower($country);
		$l_country_original = strtolower($country_original);

		$selectedCountry = null;

		foreach ($countries as $c)
		{
			// check percentage similarity
			$percent = 0;

			similar_text($l_country, strtolower($c->name), $percent);

			// select the country with greater similarity
			if ($max < $percent && $percent > 90)
			{
				$max = $percent;
				$selectedCountry = $c;
			}

			// select by code
			$code = strtolower($c->code);
			if ($code == $l_country || $code == $l_country_original)
			{
				$selectedCountry = $c;
				break;
			}
		}

		// if not country was selected, display an error message
		if (is_null($selectedCountry))
		{
			$response = new Response();
			$response->setResponseSubject("No reconocimos el pais seleccionado, selecciona ahora de esta lista");
			$response->createFromTemplate("profile_edit_country.tpl", array('countries' => $countries));
			return $response;
		}

		// update country and return empty response
		$this->update("country='{$selectedCountry->code}'", $request->email);
		return new Response();
	}

	/**
	 * Subservice CIUDAD
	 *
	 * @param Request $request
	 */
	public function _ciudad (Request $request)
	{
		$query = trim($request->query);

		if ( ! empty($query))
		{
			$this->update("city = '{$query}'", $request->email);
		}

		return new Response();
	}

	/**
	 * Subservice SEXO
	 *
	 * @param Request $request
	 */
	public function _sexo (Request $request)
	{
		return $this->subserviceEnum($request, 'gender', array(
				'F',
				'M'
		), 'Diga su sexo', null, array(
				'FEMENINO' => 'F',
				'MUJER' => 'F',
				'MASCULINO' => 'M',
				'HOMBRE' => 'M'
		));
	}

	/**
	 * Subservice NIVEL
	 *
	 * @param Request $request
	 */
	public function _nivel (Request $request)
	{
		return $this->subserviceEnum($request, 'highest_school_level',
				array(
						'PRIMARIO',
						'SECUNDARIO',
						'TECNICO',
						'UNIVERSITARIO',
						'POSTGRADUADO',
						'DOCTORADO',
						'OTRO'
				), 'Diga su nivel escolar', 'ESCOLAR');
	}

	/**
	 * Subservice alias for NIVEL
	 *
	 * @param Request $request
	 */
	public function _nivelescolar (Request $request)
	{
		return $this->_nivel($request);
	}

	/**
	 * Subservice ESTADO
	 *
	 * @param Request $request
	 *
	 * @return Response/void
	 */
	public function _estado (Request $request)
	{
		return $this->subserviceEnum($request, 'marital_status', array(
				'SOLTERO',
				'SALIENDO',
				'COMPROMETIDO',
				'CASADO'
		), 'Diga su estado civil', 'CIVIL');
	}

	/**
	 * Subservice ESTADO alias
	 *
	 * @param Request $request
	 */
	public function _estadocivil (Request $request)
	{
		return $this->_estado($request);
	}

	/**
	 * Subservice PELO
	 *
	 * @param Request $request
	 */
	public function _pelo (Request $request)
	{
		return $this->subserviceEnum($request, 'hair',
			array(
				'TRIGUENO',
				'CASTANO',
				'RUBIO',
				'NEGRO',
				'ROJO',
				'BLANCO',
				'OTRO'),
			'Diga su color de pelo');
	}

	/**
	 * Subservice OJOS
	 *
	 * @param Request $request
	 */
	public function _ojos (Request $request)
	{
		return $this->subserviceEnum($request, 'eyes', array(
				'NEGRO',
				'CARMELITA',
				'VERDE',
				'AZUL',
				'AVELLANA',
				'OTRO'
		), 'Diga el color de sus ojos', null, array(
				'NEGROS' => 'NEGRO',
				'CARMELITAS' => 'CARMELITA',
				'VERDES' => 'VERDE',
				'AZULES' => 'AZUL'
		));
	}

	/**
	 * Subservice CUERPO
	 *
	 * @param Request $request
	 */
	public function _cuerpo (Request $request)
	{
		return $this->subserviceEnum($request, 'body_type', array('DELGADO','MEDIO','EXTRA','ATLETICO'), 'Diga como es su cuerpo');
	}

	/**
	 * Subservice INTERESES
	 *
	 * @param Request $request
	 */
	public function _intereses (Request $request)
	{
		return $this->subserviceSimple($request, 'interests');
	}

	/**
	 * Subservice FOTO
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _foto ($request)
	{
		// was the image attached?
		if (count($request->attachments) > 0)
		{
			// get the first image attached
			foreach ($request->attachments as $attach)
			{
				if (is_string($attach))
				{
					$filePath = $attach;
					$attach = new stdClass();
					$attach->type = image_type_to_mime_type(IMAGETYPE_JPEG);;
					$attach->path = $filePath;
				}

				if ($attach->type == "image/jpeg")
				{
					// get the path to the image
					$di = \Phalcon\DI\FactoryDefault::getDefault();
					$wwwroot = $di->get('path')['root'];

					// create a new random image name and path
					$fileName = md5($request->email . rand());
					$filePath = "$wwwroot/public/profile/$fileName.jpg";

					// save the original copy
					@copy($attach->path, $filePath);
					$this->utils->optimizeImage($filePath);

					// make the changes in the database
					$this->update("picture='$fileName'", $request->email);
					break;
				}
			}
		}

		return new Response();
	}

	/**
	 * Subservice ORIENTACION SEXUAL
	 *
	 * @param Request $request
	 */
	public function _orientacion (Request $request)
	{
		return $this->subserviceEnum($request, 'sexual_orientation', array(
				'HETERO',
				'HOMO',
				'BI'
		), 'Diga su orientacion sexual', 'SEXUAL', array(
				'HOMOSEXUAL' => 'HOMO',
				'HETEROSEXUAL' => 'HETERO',
				'BISEXUAL' => 'BI',
				'GAY' => 'HOMO'
		));
	}

	/**
	 * Alias for subservice ORIENTACION SEXUAL
	 *
	 * @param Request $request
	 */
	public function _orientacionsexual (Request $request)
	{
		return $this->_orientacion($request);
	}

	/**
	 * Subservice piel
	 *
	 * @param Request $request
	 */
	public function _piel (Request $request)
	{
		return $this->subserviceEnum($request, 'skin', array(
				'NEGRO',
				'BLANCO',
				'MESTIZO',
				'OTRO'
		), 'Diga su color de piel', null, array(
				'BLANCA' => 'BLANCO',
				'NEGRA' => 'NEGRO',
				'MESTIZA' => 'MESTIZO'
		));
	}

	/**
	 * Show the edit mode template
	 *
	 * @param Request $request
	 * @return Response
	 *
	 */
	public function _editar (Request $request)
	{
		// get the person to edit profile
		$person = $this->utils->getPerson($request->email);

		// do not continue for non-existent users
		if (empty($person)) return new Response();

		// make the person's text readable
		$person->province = str_replace("_", " ", $person->province);
		if ($person->gender == 'M') $person->gender = "Masculino";
		if ($person->gender == 'F') $person->gender = "Femenino";
		$person->country_name = $this->utils->getCountryNameByCode($person->country);
		$person->interests = count($person->interests);
		$image = $person->picture ? array($person->picture_internal) : array();

		// create date for the selects
		$options = new stdClass();

		// gender
		$options->gender = json_encode([
			["caption"=>"Masculino", "href"=>"PERFIL SEXO MASCULINO"],
			["caption"=>"Femenino", "href"=>"PERFIL SEXO FEMENINO"]
		]);

		// sexual orientation
		$options->sexual_orientation = json_encode([
			["caption"=>"Hetero", "href"=>"PERFIL ORIENTACION HETERO"],
			["caption"=>"Gay", "href"=>"PERFIL ORIENTACION HOMO"],
			["caption"=>"Bi", "href"=>"PERFIL ORIENTACION BI"]
		]);

		// body type
		$options->body_type = json_encode([
			["caption"=>"Delgado", "href"=>"PERFIL CUERPO DELGADO"],
			["caption"=>"Medio", "href"=>"PERFIL CUERPO MEDIO"],
			["caption"=>"Extra", "href"=>"PERFIL CUERPO EXTRA"],
			["caption"=>"Atletico", "href"=>"PERFIL CUERPO ATLETICO"]
		]);

		// eyes
		$options->eyes = json_encode([
			["caption"=>"Negro", "href"=>"PERFIL OJOS NEGRO"],
			["caption"=>"Carmelita", "href"=>"PERFIL OJOS CARMELITA"],
			["caption"=>"Verde", "href"=>"PERFIL OJOS VERDE"],
			["caption"=>"Azul", "href"=>"PERFIL OJOS AZUL"],
			["caption"=>"Avellana", "href"=>"PERFIL OJOS AVELLANA"],
			["caption"=>"Otro", "href"=>"PERFIL OJOS OTRO"]
		]);

		// hair
		$options->hair = json_encode([
			["caption"=>"Trigueno", "href"=>"PERFIL PELO TRIGUENO"],
			["caption"=>"Castano", "href"=>"PERFIL PELO CASTANO"],
			["caption"=>"Rubio", "href"=>"PERFIL PELO RUBIO"],
			["caption"=>"Negro", "href"=>"PERFIL PELO NEGRO"],
			["caption"=>"Rojo", "href"=>"PERFIL PELO ROJO"],
			["caption"=>"Blanco", "href"=>"PERFIL PELO BLANCO"],
			["caption"=>"Otro", "href"=>"PERFIL PELO OTRO"]
		]);

		// skin
		$options->skin = json_encode([
			["caption"=>"Blanco", "href"=>"PERFIL PIEL BLANCO"],
			["caption"=>"Negra", "href"=>"PERFIL PIEL NEGRO"],
			["caption"=>"Mestizo", "href"=>"PERFIL PIEL MESTIZO"],
			["caption"=>"Otro", "href"=>"PERFIL PIEL OTRO"]
		]);

		// marital status
		$options->marital_status = json_encode([
			["caption"=>"Soltero", "href"=>"PERFIL ESTADO SOLTERO"],
			["caption"=>"Saliendo", "href"=>"PERFIL ESTADO SALIENDO"],
			["caption"=>"Comprometido", "href"=>"PERFIL ESTADO COMPROMETIDO"],
			["caption"=>"Casado", "href"=>"PERFIL ESTADO CASADO"]
		]);

		// highest school level
		$options->highest_school_level = json_encode([
			["caption"=>"Primario", "href"=>"PERFIL NIVEL PRIMARIO"],
			["caption"=>"Secundario", "href"=>"PERFIL NIVEL SECUNDARIO"],
			["caption"=>"Tecnico", "href"=>"PERFIL NIVEl TECNICO"],
			["caption"=>"Universitario", "href"=>"PERFIL NIVEl UNIVERSITARIO"],
			["caption"=>"Postgraduado", "href"=>"PERFIL NIVEl POSTGRADUADO"],
			["caption"=>"Doctorado", "href"=>"PERFIL NIVEl DOCTORADO"],
			["caption"=>"Otro", "href"=>"PERFIL NIVEl OTRO"]
		]);

		// province
		$options->province = json_encode([
			["caption"=>"Pinar del Rio", "href"=>"PERFIL PROVINCIA PINAR_DEL_RIO"],
			["caption"=>"La Habana", "href"=>"PERFIL PROVINCIA LA_HABANA"],
			["caption"=>"Artemisa", "href"=>"PERFIL PROVINCIA ARTEMISA"],
			["caption"=>"Mayabeque", "href"=>"PERFIL PROVINCIA MAYABEQUE"],
			["caption"=>"Matanzas", "href"=>"PERFIL PROVINCIA MATANZAS"],
			["caption"=>"Villa Clara", "href"=>"PERFIL PROVINCIA VILLA CLARA"],
			["caption"=>"Cienfuegos", "href"=>"PERFIL PROVINCIA CIENFUEGOS"],
			["caption"=>"Sancti Spiritus", "href"=>"PERFIL PROVINCIA SANCTI_SPIRITUS"],
			["caption"=>"Ciego de Avila", "href"=>"PERFIL PROVINCIA CIEGO_DE_AVILA"],
			["caption"=>"Camaguey", "href"=>"PERFIL PROVINCIA CAMAGUEY"],
			["caption"=>"Las Tunas", "href"=>"PERFIL PROVINCIA LAS_TUNAS"],
			["caption"=>"Holguin", "href"=>"PERFIL PROVINCIA HOLGUIN"],
			["caption"=>"Granma", "href"=>"PERFIL PROVINCIA GRANMA"],
			["caption"=>"Santiago de Cuba", "href"=>"PERFIL PROVINCIA SANTIAGO_DE_CUBA"],
			["caption"=>"Guantanamo", "href"=>"PERFIL PROVINCIA GUANTANAMO"],
			["caption"=>"Isla de la Juventud", "href"=>"PERFIL PROVINCIA ISLA_DE_LA_JUVENTUD"]
		]);

		// country name
		$options->country_name = json_encode([
			["href"=>"PERFIL PAIS CU", "caption"=>"Cuba"],
			["href"=>"PERFIL PAIS US", "caption"=>"Estados Unidos"]
		]);

		// religion
		$options->religion = json_encode([
			["caption"=>"Cristianismo", "href"=>"PERFIL RELIGION CRISTIANISMO"],
			["caption"=>"Catolicismo", "href"=>"PERFIL RELIGION CATOLICISMO"],
			["caption"=>"Yoruba", "href"=>"PERFIL RELIGION YORUBA"],
			["caption"=>"Protestante", "href"=>"PERFIL RELIGION PROTESTANTE"],
			["caption"=>"Santero", "href"=>"PERFIL RELIGION SANTERO"],
			["caption"=>"Abakua", "href"=>"PERFIL RELIGION ABAKUA"],
			["caption"=>"Budismo", "href"=>"PERFIL RELIGION BUDISMO"],
			["caption"=>"Judaismo", "href"=>"PERFIL RELIGION JUDAISMO"],
			["caption"=>"Islam", "href"=>"PERFIL RELIGION ISLAM"],
			["caption"=>"Ateismo", "href"=>"PERFIL RELIGION ATEISMO"],
			["caption"=>"Agnosticismo", "href"=>"PERFIL RELIGION AGNOSTICISMO"],
			["caption"=>"Secularismo", "href"=>"PERFIL RELIGION SECULARISMO"],
			["caption"=>"Otra", "href"=>"PERFIL RELIGION OTRA"]
		]);

		// prepare response for the view
		$response = new Response();
		$response->setResponseSubject('Edite su perfil');
		$response->createFromTemplate('profile_edit.tpl', array("person"=>$person, "options"=>$options), $image);
		return $response;
	}

	/**
	 * Display your relations
	 *
	 * @author kuma
	 * @version 1.0
	 * @param Request $request
	 * @return Response
	 */
	public function _relaciones(Request $request)
	{
		// connect to db
		$connection = new Connection();

		// prepare response
		$response = new Response();

		// requestor email
		$e = $request->email;

		// getting relations
		$sql = "SELECT 'amigo' as what, user2 as who, inserted as since FROM relations WHERE user1 = '$e' AND type = 'friend' AND confirmed = 1
				UNION SELECT 'amigo' as what, user1 as who, inserted as since FROM relations WHERE user2 = '$e' AND type = 'friend' AND confirmed = 1
				UNION SELECT 'bloqueado' as what, user2 as who, inserted as since FROM relations WHERE user1 = '$e' AND type = 'blocked'
				UNION SELECT 'te bloque&oacute;' as what, user1 as who, inserted as since FROM relations WHERE user2 = '$e' AND type = 'blocked'
				UNION SELECT 'siguiendo' as what, user2 as who, inserted as since FROM relations WHERE user1 = '$e' AND type = 'follow'
				UNION SELECT 'seguidor' as what, user1 as who, inserted as since FROM relations WHERE user2 = '$e' AND type = 'follow'
				UNION SELECT 'te gusta' as what, user2 as who, inserted as since FROM relations WHERE user1 = '$e' AND type = 'like'
				UNION SELECT 'admirador' as what, user1 as who, inserted as since FROM relations WHERE user2 = '$e' AND type = 'like'
				UNION SELECT 'lo tocaste' as what, user2 as who, inserted as since FROM relations WHERE user1 = '$e' AND type = 'touch'
				UNION SELECT 'te dio un toque' as what, user1 as who, inserted as since FROM relations WHERE user2 = '$e' AND type = 'touch'
				UNION SELECT 'contacto' as what, user1 as who, inserted as since FROM relations WHERE user2 = '$e' AND type = 'contact'
				UNION SELECT 'contacto' as what, user2 as who, inserted as since FROM relations WHERE user1 = '$e' AND type = 'contact'
				UNION SELECT 'ignorado' as what, user1 as who, inserted as since FROM relations WHERE user2 = '$e' AND type = 'ignore'
				UNION SELECT 'te ignora' as what, user2 as who, inserted as since FROM relations WHERE user1 = '$e' AND type = 'ignore'";

		$relations = $connection->query(" SELECT * FROM ($sql) subq ORDER BY who;");

		foreach ($relations as $k => $v)
		{
			$relations[$k]->who = $this->utils->getPerson($v->who);
		}

		// send relations
		if (isset($relations[0]))
		{
			$response->setResponseSubject("Tus relaciones");
			$response->createFromTemplate('relations.tpl', array(
				'relations' => $relations
			));

			return $response;
		}

		// get social services
		$services = $connection->query("SELECT * FROM service WHERE category = 'social';");

		// send suggestions
		$response->setResponseSubject("Te invitamos a socializar");
		$response->createFromTemplate('norelations.tpl', array('services' => $services));

		return $response;
	}

	/**
	 * Change all params at the same time
	 *
	 * @author salvipascual
	 * @version 1.0
	 * @param Request $request
	 * @return Response
	 */
	public function _bulk(Request $request)
	{
		// get the JSON with the bulk
		$json = json_decode($request->query);

		// if the method exist, call it
		if (is_object($json)) foreach ($json as $key=>$value)
		{
			$key = strtolower($key);
			if(method_exists($this, "_$key"))
			{
				$req = new Request();
				$req->email = $request->email;
				$req->subject = "PERFIL $key $value";
				$req->service = "PERFIL";
				$req->subservice = $key;
				$req->query = $value;
				$req->attachments = $request->attachments;
				$function = "_$key";
				$this->$function($req);
			}
		}

		return new Response();
	}

	/**
	 * Change state if within the US
	 * AL,AK,AS,AZ,AR,CA,CO,CT,DE,DC,FL,GA,GU,HI,ID,IL,IN,IA,KS,KY,LA,ME,MD,MH,MA,MI,FM,MN,MS,MO,MT,NE,NV,NH,NJ,NM,NY,NC,ND,MP,OH,OK,OR,PW,PA,PR,RI,SC,SD,TN,TX,UT,VT,VA,VI,WA,WV,WI,WY
	 *
	 * @author salvipascual
	 * @version 1.0
	 * @param Request $request
	 * @return Response
	 */
	public function _usstate(Request $request)
	{
		// list of US states
		$state = strtoupper($request->query);
		$states = array("AL","AK","AS","AZ","AR","CA","CO","CT","DE","DC","FL","GA","GU","HI","ID","IL","IN","IA","KS","KY","LA","ME","MD","MH","MA","MI","FM","MN","MS","MO","MT","NE","NV","NH","NJ","NM","NY","NC","ND","MP","OH","OK","OR","PW","PA","PR","RI","SC","SD","TN","TX","UT","VT","VA","VI","WA","WV","WI","WY");

		// save US state
		if(in_array($state, $states)) $this->update("usstate='$state', country='US', province=NULL", $request->email);
		return new Response();
	}

	/**
	 * Change language
	 *
	 * @author salvipascual
	 * @version 1.0
	 * @param Request $request
	 * @return Response
	 */
	public function _lang(Request $request)
	{
		// get the values from the post
		$email = $request->email;
		$lang = $request->query;

		// check if the language exist
		$connection = new Connection();
		$test = $connection->query("SELECT * FROM languages WHERE code = '$lang'");
		if(empty($test)) return new Response();

		// set the new language for the user
		$connection->query("UPDATE person SET lang='$lang' WHERE email='$email'");
		return new Response();
	}

	/**
	 * Get the global info for the General app
	 *
	 * @author salvipascual
	 * @api
	 * @version 1.0
	 * @param Request $request
	 * @return Response
	 */
	public function _status(Request $request)
	{
		// get data for the app
		$res = $this->utils->getExternalAppData($request->email, $request->query);

		// respond back to the API
		$response = new Response();
		$response->attachments = $res["attachments"];
		return $response->createFromJSON($res["json"]);
	}

	/**
	 * Change the image quality to send to the user
	 *
	 * @author salvipascual
	 * @api
	 * @version 1.0
	 * @param Request $request
	 * @return Response
	 */
	public function _imagen(Request $request)
	{
		// list of possible values
		$quality = strtoupper($request->query);
		$values = array('ORIGINAL', 'REDUCIDA', 'SIN_IMAGEN');

		// save the image quality
		if(in_array($quality, $values)) $this->update("img_quality='$quality'", $request->email);
		return new Response();
	}

	/**
	 * Update a profile
	 *
	 * @param String $sqlset
	 * @param String $email
	 */
	private function update ($sqlset, $email)
	{
		$query = "UPDATE person SET $sqlset, last_update_date=CURRENT_TIMESTAMP, updated_by_user=1	WHERE email='$email'";
		$query = preg_replace("/\s+/", " ", $query);
		$connection = new Connection();
		$connection->query($query);
	}

	/**
	 * Subservice utility for ENUM profile fields
	 *
	 * @param Request $request
	 * @param String $field
	 * @param array $enum
	 * @param String $wrong_template
	 * @param String $wrong_subject
	 * @param String $field
	 *
	 * @return Response/void
	 */
	private function subserviceEnum (Request $request, $field, $enum, $wrong_subject, $prefix = null, $synonymous = array())
	{
		if ( ! is_null($prefix))
		{
			if (stripos($request->query, $prefix) === 0)
			{
				$request->query = trim(substr($request->query, strlen($prefix)));
			}
		}

		$query = strtoupper(trim($request->query));

		// if the query is empty, set to null the field
		if (empty($query))
		{
			return new Response();
		}
		else
		{
			// search for $synonymous
			if (isset($synonymous[$query])) $query = $synonymous[$query];

			// search query in the list
			if (array_search($query, $enum) !== false)
			{
				// update the field
				$this->update("$field = '$query'", $request->email);
				return new Response();
			}
			else
			{
				// wrong query, return a response with selectable list
				$response = new Response();
				$response->setResponseSubject($wrong_subject);

				// NOTE: The template name include the field name

				// clear underscores
				foreach ($enum as $k => $v)
				{
					$enum[$k] = str_replace('_', ' ', $v);
				}

				$response->createFromTemplate('wrong_' . $field . '.tpl', array('list' => $enum));
				return $response;
			}
		}
	}

	/**
	 * Subservice utitlity for simple profile fields
	 *
	 * @param Request $request
	 * @param String $field
	 * @param String $prefix
	 */
	private function subserviceSimple (Request $request, $field, $prefix = null)
	{
		if ( ! is_null($prefix))
		{
			if (stripos($request->query, $prefix) === 0)
			{
				$request->query = trim(substr($request->query, strlen($prefix)));
			}
		}

		$value = trim($request->query);
		$value = str_replace(array("'","`"), "", $value);

		if ( ! empty($value))
		{
			$this->update("$field = '$value'", $request->email);
		}

		return new Response();
	}

	/**
	 * Subservice utility for date profile fields
	 *
	 * @param Request $request
	 * @param String $field
	 * @param String $prefix
	 */
	private function subserviceDate (Request $request, $field, $prefix = null)
	{
		if ( ! is_null($prefix))
		{
			if (stripos($request->query, $prefix) === 0)
			{
				$request->query = trim(substr($request->query, strlen($prefix)));
			}
		}

		$query = trim($request->query);

		// read date in Spanish
		setlocale(LC_ALL, "es_ES");

		// try getting the date
		$date = DateTime::createFromFormat("d/m/Y", $query);

		// if date could not be calculated, return null
		if (empty($date)) return new Response();
		else $query = "'" . strftime("%Y-%m-%d", $date->getTimestamp()) . "'";

		$this->update("$field = $query", $request->email);

		return new Response();
	}
}
