<?php

class Perfil extends Service
{
	private $origins = [];

	public function __construct() 
	{
		// keep origins in just one place to update them easily
		$this->origins = ["Amigo en Cuba", "Familia Afuera", "Referido", 
			"El Paquete", "Revolico", "Casa de Apps", "Facebook", "Internet",
			"La Calle", "Prensa Independiente", "Prensa Cubana", "Otro"];
	}

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
		if ( ! filter_var($emailToLookup, FILTER_VALIDATE_EMAIL)) {
			$emailToLookup = Utils::getEmailFromUsername($emailToLookup);
		}

		// check if the person exist. If not, message the requestor
		if ( ! Utils::personExist($emailToLookup)) {
			$response = new Response();
			$response->setResponseSubject("No encontramos un perfil para ese usuario");
			$response->createFromText("Lo sentimos, pero no encontramos al usuario que esta buscando. Por favor revise que cada letra sea correcta e intente nuevamente.");
			return $response;
		}

		// get the person
		$person = Connection::query("SELECT * FROM person WHERE email = '$emailToLookup'");

		// prepare the full profile
		$social = new Social();
		$profile = $social->prepareUserProfile($person[0], $request->lang);

		// check if current user blocked the user to lookup, or is blocked by
		// and get the number of tickets for the raffle
		$profile->blocked = false;
		$profile->blockedByMe = false;
		if ($emailToLookup==$request->email) {
			$tickets = Connection::query("SELECT count(ticket_id) as tickets FROM ticket WHERE raffle_id is NULL AND email = '$emailToLookup'")[0]->tickets;
		}
		else {
			$tickets=0;
			$blocks=$this->isBlocked($request->email,$emailToLookup);
			$profile->blocked=$blocks->blocked;
			$profile->blockedByMe=$blocks->blockedByMe;
		}

		if ($profile->blocked) {
			$response = new Response();
			$response->setResponseSubject("Lo sentimos, usted no tiene acceso a este perfil");
			$response->createFromTemplate("blocked.tpl",['profile'=>$profile]);
			return $response;
		}

		// pass variables to the template
		$responseContent = [
			"profile" => $profile,
			"tickets" => $tickets,
			"ownProfile" => $emailToLookup == $request->email
		];

		// pass profile image to the response
		$image=[];
		if ($profile->picture) $image[]=$profile->picture_internal;

		foreach ($profile->extraPictures_internal as $picture){
			$image[]=$picture;
		}

		// create a new Response object and input the template and the content
		$response = new Response();
		if($request->query) $response->setCache("day");
		$response->setResponseSubject("Perfil de Apretaste");
		$response->createFromTemplate("profile.tpl", $responseContent, $image);
		return $response;
	}

	/**
	 * Subservice VER to see extra pictures
	 * 
	 * @param Request
	 * @return Response
	 */
	public function _ver(Request $request)
	{
		if (empty($request->query)) return new Response();

		$wwwroot = \Phalcon\DI\FactoryDefault::getDefault()->get('path')['root'];
		$file = "$wwwroot/public/profile/$request->query.jpg";

		if (file_exists($file)) {
			$response = new Response();
			$response->subject = "Ver imagen";
			$response->createFromTemplate("verimg.tpl", ['img'=>$file],[$file]);
			return $response;
		}
	}

	/**
	 * Get if the user is blocked or has been blocked by
	 * 
	 * @author ricardo@apretaste.com
	 * @param String $user1
	 * @param String $user2
	 * @return Object
	 */
	private function isBlocked(String $user1, String $user2)
	{
		$res=new stdClass();
		$res->blocked = false;
		$res->blockedByMe = false;

		$r = Connection::query("SELECT * FROM ((SELECT COUNT(user1) AS blockedByMe FROM relations
				WHERE user1 = '$user1' AND user2 = '$user2'
				AND `type` = 'blocked' AND confirmed=1) AS A,
				(SELECT COUNT(user1) AS blocked FROM relations
				WHERE user1 = '$user2' AND user2 = '$user1'
				AND `type` = 'blocked' AND confirmed=1) AS B)");

		$res->blocked=($r[0]->blocked>0)?true:false;
		$res->blockedByMe=($r[0]->blockedByMe>0)?true:false;

		return $res;
	}

	/**
	 * Subservice USERNAME
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _username (Request $request)
	{
		// clean, shorten and lowercase the text
		$username = preg_replace("/[^a-zA-Z0-9]+/", "", $request->query);
		$username = strtolower(substr($username, 0, 15));

		// check if the username exist, else recreate it
		$exist = Connection::query("SELECT COUNT(id) AS exist FROM person WHERE username='$username'");
		if($exist[0]->exist) $username = Utils::usernameFromEmail($request->email);

		// update the username in the database
		$this->updateEscape('username', $username, $request->userId, 15);
		return new Response();
	}

	/**
	 * Subservice NOMBRE
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _nombre (Request $request)
	{
		// get the pieces of names
		$n = Utils::fullNameToNamePieces(trim($request->query));
		if ( ! is_array($n)) return new Response();

		for($i=0; $i<4; $i++) $n[$i] = substr($n[$i], 0 , 50);

		// create the query
		$query = " first_name='{$n[0]}', middle_name='{$n[1]}', last_name='{$n[2]}', mother_name='{$n[3]}'";

		// update the name in the database
		$this->update($query, $request->userId);
		return new Response();
	}

	/**
	 * Subservice PHONE
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _phone(Request $request)
	{
		// remove all non-numeric characters from the phone
		$phone = preg_replace('/[^0-9.]+/', '', $request->query);

		// is cell or not?
		$field = 'phone';
		if(substr($phone, 0, 1) == '5') $field = 'cellphone';

		// update phone
		$this->updateEscape($field, $phone, $request->userId, 10);
		return new Response();
	}

	/**
	 * Subservice TELEFONO
	 * This function call to _phone for backward compatibility
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _telefono (Request $request)
	{
		return $this->_phone($request);
	}

	/**
	 * Subservice CUMPLEANOS
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _cumpleanos (Request $request)
	{
		// clean the date passed
		$query = trim($request->query);

		// calculate the date passed the user
		$date = (strlen($query)<8)?DateTime::createFromFormat("j-n-y", $query):DateTime::createFromFormat("d/m/Y", $query);
		if(empty($date)) try {
			$date = new DateTime($query);
		} catch(Exception $e) {
			$this->utils->addNotification($request->email, "perfil", "Ingreso un formato de fecha no reconocido en su fecha de cumpleaños, por favor use las opciones de la app", "PERFIL");
			return new Response();
		}

		$time = strtotime("-10 year", time());
		$minBirthDate = DateTime::createFromFormat("U", $time);

		$age=date_diff($date,date_create('today'))->y;
		if ($date>=$minBirthDate || $age>110) {
			$this->utils->addNotification($request->email, "perfil", "Su edad debe ser mayor a 10 años y menor a 110 años, no ingrese datos falsos", "PERFIL");
			return new Response();
		}

		// save date in the database
		$dtStr = strftime("%Y-%m-%d", $date->getTimestamp());
		$this->update("date_of_birth='$dtStr'", $request->userId);
		return new Response();
	}

	/**
	 * Subservice DESCRIPCION
	 *
	 * @param Request
	 * @return Response
	 */
	public function _descripcion(Request $request)
	{
		$description=strip_tags(trim($request->query));
		if (empty($description) || strlen($description)<100) return new Response();

		$this->update("about_me='$description'", $request->userId);
		return new Response();
	}

	/**
	 * Subservice PROFESION
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _profesion (Request $request)
	{
		$this->updateEscape('occupation', $request->query, $request->userId, 50);
		return new Response();
	}

	/**
	 * Subservice RELIGION
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _religion (Request $request)
	{
		// list of religions
		$enums = [
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
			'PROTESTANTE',
			'CRISTIANISMO',
			'OTRA'];

		// update the value on the database
		$this->updateEnum($request->query, $enums, 'OTRA', 'religion', $request->userId);
		return new Response();
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
		$enums = [
			'PINAR_DEL_RIO', 'LA_HABANA', 'ARTEMISA', 'MAYABEQUE',
			'MATANZAS', 'VILLA_CLARA', 'CIENFUEGOS', 'SANCTI_SPIRITUS',
			'CIEGO_DE_AVILA', 'CAMAGUEY', 'LAS_TUNAS', 'HOLGUIN',
			'GRANMA', 'SANTIAGO_DE_CUBA', 'GUANTANAMO', 'ISLA_DE_LA_JUVENTUD'
		];

		// save country as Cuba
		$this->update("country='CU', usstate=NULL", $request->userId);

		// update the value on the database
		$this->updateEnum($request->query, $enums, '', 'province', $request->userId);
		return new Response();
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
		$countries = Connection::query("SELECT code, es AS name FROM countries ORDER BY code");
		$country = trim($request->query);
		$country_original = $country;

		// do not let empty countries
		if (empty($country)) return new Response();

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

		foreach ($countries as $c) {
			// check percentage similarity
			$percent = 0;

			similar_text($l_country, strtolower($c->name), $percent);

			// select the country with greater similarity
			if ($max < $percent && $percent > 90) {
				$max = $percent;
				$selectedCountry = $c;
			}

			// select by code
			$code = strtolower($c->code);
			if ($code == $l_country || $code == $l_country_original) {
				$selectedCountry = $c;
				break;
			}
		}

		// if not country was selected, do nothing
		if (is_null($selectedCountry)) return new Response();

		// update country and return empty response
		$this->update("country='{$selectedCountry->code}'", $request->userId);
		return new Response();
	}

	/**
	 * Subservice CIUDAD
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _ciudad (Request $request)
	{
		$this->updateEscape('city', $request->query, $request->userId, 100);
		return new Response();
	}

	/**
	 * Subservice SEXO
	 *
	 * @author salvipascual
	 * @param Request $request
	 * @return Response
	 */
	public function _sexo (Request $request)
	{
		// get values
		$gender = $request->query[0];
		$enums = ['F','M'];

		// update the value on the database
		$this->updateEnum($gender, $enums, '', 'gender', $request->userId);
		return new Response();
	}

	/**
	 * Subservice NIVEL
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _nivel (Request $request)
	{
		// list of possible values
		$enums = ['PRIMARIO','SECUNDARIO','TECNICO','UNIVERSITARIO','POSTGRADUADO','DOCTORADO','OTRO'];

		// update the value on the database
		$this->updateEnum($request->query, $enums, 'OTRO', 'highest_school_level', $request->userId);
		return new Response();
	}

	/**
	 * Subservice alias for NIVEL
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _nivelescolar (Request $request)
	{
		return $this->_nivel($request);
	}

	/**
	 * Subservice ESTADO
	 *
	 * @param Request $request
	 * @return Response/void
	 */
	public function _estado (Request $request)
	{
		// list of possible values
		$enums = ['SOLTERO','SALIENDO','COMPROMETIDO','CASADO','OTRO'];
	
		// update the value on the database
		$this->updateEnum(strtoupper($request->query), $enums, 'OTRO', 'marital_status', $request->userId);
		return new Response();
	}

	/**
	 * Subservice ESTADO alias
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _estadocivil (Request $request)
	{
		return $this->_estado($request);
	}

	/**
	 * Subservice PELO
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _pelo (Request $request)
	{
		// list of possible values
		$enums = ['TRIGUENO','CASTANO','RUBIO','NEGRO','ROJO','BLANCO','OTRO'];

		// update the value on the database
		$this->updateEnum($request->query, $enums, 'OTRO', 'hair', $request->userId);
		return new Response();
	}

	/**
	 * Subservice OJOS
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _ojos (Request $request)
	{
		// list of possible values
		$enums = ['NEGRO','CARMELITA','VERDE','AZUL','AVELLANA','OTRO'];

		// update the value on the database
		$this->updateEnum($request->query, $enums, 'OTRO', 'eyes', $request->userId);
		return new Response();
	}

	/**
	 * Subservice CUERPO
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _cuerpo (Request $request)
	{
		// list of possible values
		$enums = ['DELGADO', 'MEDIO', 'EXTRA', 'ATLETICO'];

		// update the value on the database
		$this->updateEnum($request->query, $enums, '', 'body_type', $request->userId);
		return new Response();
	}

	/**
	 * Subservice INTERESES
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _intereses (Request $request)
	{
		$this->updateEscape('interests', $request->query, $request->userId, 1000);
		return new Response();
	}

	/**
	 * Subservice FOTO
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _foto ($request)
	{
		// is the image passed in the subject? (web only)
		if(file_exists($request->query)) {
			$request->attachments = array($request->query);
		}

		// is the image attached (email and app)?
		if($request->attachments) foreach ($request->attachments as $attach)
		{
			// get the first image attached
			$mimetype = explode("/", mime_content_type($attach))[0];
			if($mimetype != "image") continue;

			// get the path to the image
			$di = \Phalcon\DI\FactoryDefault::getDefault();
			$wwwroot = $di->get('path')['root'];

			// create a new random image name and path
			$fileName = md5($request->email . rand());
			$filePath = "$wwwroot/public/profile/$fileName.jpg";

			// save the original copy
			@copy($attach, $filePath);
			$this->utils->optimizeImage($filePath);

			// make the changes in the database
			$this->update("picture='$fileName'", $request->userId);
			break;
		}

		return $this->_editar($request);
	}

	/**
	 * Subservice FOTO
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _extrafoto ($request)
	{
		// is the image passed in the subject? (web only)
		if(file_exists($request->query)) {
			$request->attachments = array($request->query);
		}

		// is the image attached (email and app)?
		if($request->attachments) foreach ($request->attachments as $attach)
		{
			// get the first image attached
			$mimetype = explode("/", mime_content_type($attach))[0];
			if($mimetype != "image") continue;

			$person = Connection::query("SELECT picture,extra_pictures FROM person WHERE email = '$request->email'")[0];
			$pics=json_decode($person->extra_pictures,true);

			// get the path to the image
			$di = \Phalcon\DI\FactoryDefault::getDefault();
			$wwwroot = $di->get('path')['root'];

			// create a new random image name and path
			$fileName = md5($request->email . rand());
			$filePath = "$wwwroot/public/profile/$fileName.jpg";

			//save the new img in the array, max 4 imgs
			if (count($pics)==4) $pics[3]=$fileName;
			else $pics[]=$fileName;

			$pics=json_encode($pics);

			// save the original copy
			@copy($attach, $filePath);
			$this->utils->optimizeImage($filePath);

			// make the changes in the database
			$this->update("extra_pictures='$pics'", $request->userId);
			break;
		}

		return $this->_editar($request);
	}

	/**
	 * Subservice ORIENTACION SEXUAL
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _orientacion (Request $request)
	{
		// list of possible values
		$enums = ['HETERO','HOMO','BI'];

		// update the value on the database
		$this->updateEnum($request->query, $enums, '', 'sexual_orientation', $request->userId);
		return new Response();
	}

	/**
	 * Alias for subservice ORIENTACION SEXUAL
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _orientacionsexual (Request $request)
	{
		return $this->_orientacion($request);
	}

	/**
	 * Subservice piel
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _piel (Request $request)
	{
		// list of possible values
		$enums = ['NEGRO','BLANCO','MESTIZO','OTRO'];

		// update the value on the database
		$this->updateEnum($request->query, $enums, '', 'skin', $request->userId);
		return new Response();
	}

	/**
	 * Show the edit mode template
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _editar (Request $request)
	{
		// get the person to edit profile
		$person = $this->utils->getPerson($request->email);
		if (empty($person)) return new Response();

		// make the person's text readable
		$person->province = str_replace("_", " ", $person->province);
		if ($person->gender == 'M') $person->gender = "Masculino";
		if ($person->gender == 'F') $person->gender = "Femenino";
		$person->country_name = $this->utils->getCountryNameByCode($person->country);
		$person->usstate_name = $this->utils->getStateNameByCode($person->usstate);
		$person->interests = count($person->interests);
		$image = $person->picture ? [$person->picture_internal] : [];

		// create the list of origins
		$origins = implode(",", $this->origins);

		// prepare response for the view
		$response = new Response();
		$response->setResponseSubject('Edite su perfil');
		$response->createFromTemplate('profile_edit.tpl', ["person"=>$person, "origins"=>$origins], $image);
		return $response;
	}

	/**
	 * Show the form of where you hear about the app
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _origen (Request $request)
	{
		// get the person to add origin
		$person = $this->utils->getPerson($request->email);
		if (empty($person)) return new Response();

		// create the list of origins
		$origins = implode(",", $this->origins);

		// prepare response for the view
		$response = new Response();
		$response->setResponseSubject('Origen de la app');
		$response->createFromTemplate('origin.tpl', ["person"=>$person, "origins"=>$origins]);
		return $response;
	}

	/**
	 * Subservice ORIGIN
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function _origin (Request $request)
	{
		// save the value
		$this->updateEnum($request->query, $this->origins, 'OTRO', 'origin', $request->userId);
		return new Response();
	}

	/**
	 * Block an user
	 *
	 * @author ricardo@apretaste.com
	 * @param Request
	 * @return Response
	 */
	public function _bloquear(Request $request)
	{
		$person=Utils::getEmailFromUsername($request->query);
		$person=Utils::getPerson($person);

		if($person){
			$r = Connection::query("
				SELECT *
				FROM `relations`
				WHERE user1 = '$request->email'
				AND user2 = '$person->email'");

			if (isset($r[0])) Connection::query("
				UPDATE `relations` SET confirmed=1
				WHERE user1='$request->email'
				AND user2='$person->email' AND `type`='blocked'");
			else Connection::query("
				INSERT INTO `relations`(user1,user2,`type`,confirmed)
				VALUES('$request->email','$person->email','blocked',1)");
		}
		return $this->_main($request);
	}

	/**
	 * unlock an user
	 *
	 * @author ricardo@apretaste.com
	 * @param Request
	 * @return Response
	 */
	public function _desbloquear(Request $request)
	{
		$person = Utils::getEmailFromUsername($request->query);
		$person = Utils::getPerson($person);

		if($person){
			Connection::query("
				UPDATE relations SET confirmed=0
				WHERE user1='$request->email'
				AND user2='$person->email' AND `type`='blocked'");
		}
		return $this->_main($request);
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
		// getting relations
		$e = $request->email;
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
		$relations = Connection::query("SELECT * FROM ($sql) subq ORDER BY who;");

		// get profiles from relations
		foreach ($relations as $k => $v) {
			$relations[$k]->who = $this->utils->getPerson($v->who);
		}

		// send relations
		if (isset($relations[0])) {
			$response = new Response();
			$response->setResponseSubject("Tus relaciones");
			$response->createFromTemplate('relations.tpl', ['relations' => $relations]);
			return $response;
		}

		// send suggestions
		$response->setResponseSubject("Te invitamos a socializar");
		$response->createFromText('Usted no tiene amistades. Encuentre amistades en la Pizarra o otros servicios sociales');
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
				$req->userId = $request->userId;
				$req->subject = "PERFIL $key $value";
				$req->service = "PERFIL";
				$req->subservice = $key;
				$req->query = $value;
				$req->attachments = $request->attachments;
				$function = "_$key";
				if (method_exists($this, $function))
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
		if(in_array($state, $states)) $this->update("usstate='$state', country='US', province=NULL", $request->userId);
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
		$test = Connection::query("SELECT * FROM languages WHERE code = '$lang'");
		if(empty($test)) return new Response();

		// set the new language for the user
		Connection::query("UPDATE person SET lang='$lang' WHERE email='$email'");
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
		$enums = ['ORIGINAL', 'REDUCIDA', 'SIN_IMAGEN'];

		// save the image quality
		if(in_array($quality, $enums)) {
			$this->update("img_quality='$quality'", $request->userId);
		}

		return new Response();
	}

	/**
	 * Update a profile
	 *
	 * @param String $sqlset
	 * @param String $userId
	 */
	private function update($sqlset, $userId)
	{
		if(empty($userId)) return new Response();

		Connection::query("
			UPDATE person 
			SET $sqlset, last_update_date=CURRENT_TIMESTAMP, updated_by_user=1 
			WHERE id='$userId'");
	}

	/**
	 * Update a profile scaping string size
	 *
	 * @param String $field : field name in the database
	 * @param String $value : value to save
	 * @param String $userId
	 * @param Integer $cut : Max length of the value
	 */
	private function updateEscape($field, $value, $userId, $cut=false)
	{
		// escape and cut the string
		$value = Connection::escape($value, $cut);

		// save to the database
		$this->update("$field='$value'", $userId);
	}

	/**
	 * Sub-service utility for simple profile fields
	 *
	 * @author salvipascual
	 * @param String $value : ASUL
	 * @param Array $enums : ['ROJO', 'VERDE', 'AZUL', 'OTRO'] 
	 * @param String $default : OTRO
	 * @param String $field : field name in the database
	 * @param integer $userId
	 */
	private function updateEnum($value, $enums, $default, $field, $userId)
	{
		// do not allow empty responses
		if(empty($value)) return new Response();

		// set initial params
		$value = strtolower($value);
		$selected = null;
		$max = 0;

		// pick the most similar value
		foreach ($enums as $enum) {
			// check percentage similarity
			$percent = 0;
			similar_text($value, strtolower($enum), $percent);

			// select the greatest similar
			if ($max < $percent && $percent > 70) {
				$max = $percent;
				$selected = $enum;
			}
		}

		// if nothing is selected, get the default value
		if( ! $selected) $selected = strtolower($default);

		// if empty, make the field NULL
		$query = empty($selected) ? "$field=NULL" : "$field='$selected'";

		// update the table
		$this->update($query, $userId);
	}
}
