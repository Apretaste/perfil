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

		// get the full profile for the person
		$profile = $this->utils->getPerson($emailToLookup);

		// get the number of tickts for the raffle
		$connection = new Connection();
		$tickets = $connection->deepQuery("SELECT count(ticket_id) as tickets FROM ticket WHERE raffle_id is NULL AND email = '$emailToLookup'");

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
		$response->setResponseSubject("Perfil de Apretaste");
		$response->createFromTemplate("profile.tpl", $responseContent, $image);
		return $response;
	}

	/**
	 * Subservice Request
	 *
	 * @param Request $request
	 */
	public function _nombre (Request $request)
	{
		$n = $this->utils->fullNameToNamePieces(trim($request->query));

		if ( ! is_array($n)) return new Response();
		for ($i = 0; $i <= 3; $i ++) $n[$i] = "'{$n[$i]}'";

		$query = " first_name = {$n[0]},
			middle_name = {$n[1]},
			last_name = {$n[2]},
			mother_name = {$n[3]}";

		$this->update($query, $request->email);
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
		$provs = array(
			'PINAR_DEL_RIO',
			'LA_HABANA',
			'ARTEMISA',
			'MAYABEQUE',
			'MATANZAS',
			'VILLA_CLARA',
			'CIENFUEGOS',
			'SANCTI_SPIRITUS',
			'CIEGO_DE_AVILA',
			'CAMAGUEY',
			'LAS_TUNAS',
			'HOLGUIN',
			'GRANMA',
			'SANTIAGO_DE_CUBA',
			'GUANTANAMO',
			'ISLA_DE_LA_JUVENTUD'
		);

		$synon = array();
		foreach ($provs as $v)
		{
			$synon[str_replace('_', ' ', $v)] = $v;
		}
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
		$connection = new Connection();
		$countries = $connection->deepQuery("SELECT code, es AS name FROM countries ORDER BY code");
		$country = trim($request->query);
		$country_original = $country;
		
		if($country == "US") $country = "Estados Unidos de America";
		if($country == "USA") $country = "Estados Unidos de America";
		if($country == "estados unidos") $country = "Estados Unidos de America";

		if (empty($country))
		{
			$response = new Response();
			$response->setResponseSubject("Selecciona el pais donde vive");
			$response->createFromTemplate("profile_edit_country.tpl", array('countries' => $countries));
			return $response;
		}

		$selected_country = null;
		$max = 0;

		$aprox = true;

		$l_country = strtolower($country);
		$l_country_original = strtolower($country_original);
		
		foreach ($countries as $c)
		{
			$percent = 0;
			$sim = similar_text(strtolower($country), strtolower($c->name), $percent);

			if ($max < $percent && $percent > 90)
			{
				$max = $percent;
				$selected_country = $c;
			}

			$code = strtolower($c->code);
			if ($code == $l_country || $code == $l_country_original)
			{
				$aprox = false;
				$selected_country = $c;
				break;
			}
		}

		if (is_null($selected_country))
		{
			$response = new Response();
			$response->setResponseSubject("No reconocimos el pais seleccionado, selecciona ahora de esta lista");
			$response->createFromTemplate("profile_edit_country.tpl", array('countries' => $countries));
			return $response;
		}

		$connection->deepQuery("UPDATE person SET country = '{$selected_country->code}' WHERE email = '{$request->email}';");

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
				if ($attach->type == "image/jpeg")
				{
					// get the path to the image
					$di = \Phalcon\DI\FactoryDefault::getDefault();
					$wwwroot = $di->get('path')['root'];

					// create a new random image name and path
					$fileName = md5($request->email . rand());
					$filePath = "$wwwroot/public/profile/$fileName.jpg";

					// save the original copy
					copy($attach->path, $filePath);
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

		// get readable text for province
		$person->province = str_replace("_", " ", $person->province);

		// get readable text for gender
		if ($person->gender == 'M') $person->gender = "Masculino";
		if ($person->gender == 'F') $person->gender = "Femenino";

		// get readable country
		$person->country_name = $this->utils->getCountryNameByCode($person->country);

		// save interests as string
		$person->interests = implode(", ", $person->interests);

		// get image
		$image = $person->picture ? array($person->picture_internal) : array();

		// prepare response for the view
		$response = new Response();
		$response->setResponseSubject('Edite su perfil');
		$response->createFromTemplate('profile_edit.tpl', array("person"=>$person), $image);
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

		$relations = $connection->deepQuery(" SELECT * FROM ($sql) subq ORDER BY who;");

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
		$services = $connection->deepQuery("SELECT * FROM service WHERE category = 'social';");

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
		foreach ($json as $key=>$value)
		{
			if(method_exists($this, "_$key"))
			{
				$req = new Request();
				$req->email = $request->email;
				$req->subject = "PERFIL $key $value";
				$req->service = "PERFIL";
				$req->subservice = $key;
				$req->query = $value;
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
		// get the values from the post
		$email = $request->email;
		$state = $request->query;

		// set the new language for the user
		$connection = new Connection();
		$connection->deepQuery("UPDATE person SET usstate='$state' WHERE email='$email'");
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
		$test = $connection->deepQuery("SELECT * FROM languages WHERE code = '$lang'");
		if(empty($test)) return new Response();

		// set the new language for the user
		$connection->deepQuery("UPDATE person SET lang='$lang' WHERE email='$email'");
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
		$connection->deepQuery($query);
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
