<?php

class Service
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
	 * @param Response $response
	 */
	public function _main (Request $request, Response $response){
		// get the email or the username for the profile
		$data = $request->input->data;
		$content = new stdClass();
		if(!empty($data->query)) $data->username = $data->query;
		if(!empty($data->username)){
			$user = Utils::getPersonFromUsername($data->username);
			// check if the person exist. If not, message the requestor
			if(!$user){
				$content->username = $data->username;
				$response->setTemplate("not_found.ejs",$content);
			}

			$tickets=0;
			$ownProfile = false;

			// check if current user blocked the user to lookup, or is blocked by
			$blocks = Social::isBlocked($request->person->email,$user->email);
			$user->blocked = $blocks->blocked;
			$user->blockedByMe = $blocks->blockedByMe;
			$content->profile = $user;
			if ($user->blocked) $response->setTemplate("blocked.ejs",$content);
		}
		else{
			$user = $request->person;
			$ownProfile = true;
			// and get the number of tickets for the raffle
			$tickets = Connection::query("SELECT count(ticket_id) as tickets FROM ticket WHERE raffle_id is NULL AND email = '{$user->email}'")[0]->tickets;
		}

		// prepare the full profile
		$profile = Social::prepareUserProfile($user);

		// pass profile image to the response
		$image=[];
		if ($profile->picture) $image[]=$profile->picture_internal;

		foreach ($profile->extraPictures_internal as $key => $picture){
			$image[]=$picture;
		}

		// pass variables to the template
		$content->profile = $profile;
		$content->tickets = $tickets;
		$content->ownProfile = $ownProfile;

		// create a new Response object and input the template and the content
		if(!$ownProfile) $response->setCache("day");
		$response->setTemplate("profile.ejs", $content, $image);
	}

	/**
	 * Subservice VER to see extra pictures
	 * 
	 * @param Request
	 */
	public function _ver(Request $request, Response $response)
	{
		if (empty($request->query)) return;

		$wwwroot = \Phalcon\DI\FactoryDefault::getDefault()->get('path')['root'];
		$file = "$wwwroot/public/profile/$request->query.jpg";

		if (file_exists($file)) $response->setTemplate("verimg.tpl", ['img'=>$file],[$file]);
	}

	/**
	 * Subservice USERNAME
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _username (Request $request, Response $response)
	{
		// clean, shorten and lowercase the text
		$username = preg_replace("/[^a-zA-Z0-9]+/", "", $request->query);
		$username = strtolower(substr($username, 0, 15));

		// check if the username not exist, else if not belong to the user, recreate it
		$exist = Connection::query("SELECT id AS exist FROM person WHERE username='$username'");
		if(!empty($exist) && $exist[0]->id!=$request->person->id) $username = Utils::usernameFromEmail($username);

		// update the username in the database
		$this->updateEscape('username', $username, $request->person->id, 15);
	}

	/**
	 * Subservice NOMBRE
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _nombre (Request $request, Response $response)
	{
		// get the pieces of names
		$n = Utils::fullNameToNamePieces(trim($request->query));
		if ( ! is_array($n)) return;

		for($i=0; $i<4; $i++) $n[$i] = substr($n[$i], 0 , 50);

		// create the query
		$query = " first_name='{$n[0]}', middle_name='{$n[1]}', last_name='{$n[2]}', mother_name='{$n[3]}'";

		// update the name in the database
		$this->update($query, $request->person->id);
	}

	/**
	 * Subservice PHONE
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _phone(Request $request, Response $response)
	{
		// remove all non-numeric characters from the phone
		$phone = preg_replace('/[^0-9.]+/', '', $request->query);

		// is cell or not?
		$field = 'phone';
		if(substr($phone, 0, 1) == '5') $field = 'cellphone';

		// update phone
		$this->updateEscape($field, $phone, $request->person->id, 10);
	}

	/**
	 * Subservice TELEFONO
	 * This function call to _phone for backward compatibility
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _telefono (Request $request, Response $response)
	{
		return $this->_phone($request);
	}

	/**
	 * Subservice CUMPLEANOS
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _cumpleanos (Request $request, Response $response)
	{
		// get the params for old versions of the app
		if(empty($request->params[0])) $request->params = explode(" ", trim($request->query));

		// get the date passed
		$day = $request->params[0];
		$month = $request->params[1];
		$year = $request->params[2];

		// remove any string-like part (like spaces)
		$year = preg_replace('/\D/', '', $year);
		$month = preg_replace('/\D/', '', $month);
		$day = preg_replace('/\D/', '', $day);

		// get possible birth years ranges
		$yearMin = date('Y') - 90;
		$yearMax = date('Y') - 10;

		// do not save dates out of range
		if($year < $yearMin || $year > $yearMax) return;
		if($month < 1 || $month > 12) return;
		if($day < 1 || $day > 31) return;

		// save the date in the database
		$this->update("year_of_birth='$year'", $request->person->id);
		$this->update("date_of_birth='$year-$month-$day'", $request->person->id);
	}

	/**
	 * Subservice ANO de nacimiento
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _ano (Request $request, Response $response)
	{
		// get possible birth years ranges
		$yearMin = date('Y') - 90;
		$yearMax = date('Y') - 10;

		// remove any string part of the year (like spaces)
		$year = preg_replace('/\D/', '', $request->query);

		// do not save years out of range
		if($year < $yearMin || $year > $yearMax) return;

		// save date in the database
		$this->update("year_of_birth='$year'", $request->person->id);
	}

	/**
	 * Subservice DESCRIPCION
	 *
	 * @param Request
	 */
	public function _descripcion(Request $request, Response $response)
	{
		// do not allow empty or long descriptions
		$description = Connection::escape($request->query, 250);
		if (strlen($description) < 50) return;

		// set the description
		$this->update("about_me='$description'", $request->person->id);
	}

	/**
	 * Subservice PROFESION
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _profesion (Request $request, Response $response)
	{
		$this->updateEscape('occupation', $request->query, $request->person->id, 50);
	}

	/**
	 * Subservice RELIGION
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _religion (Request $request, Response $response)
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
		$this->updateEnum($request->query, $enums, 'OTRA', 'religion', $request->person->id);
	}

	/**
	 * Subservice PROVINCIA
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _provincia (Request $request, Response $response)
	{
		// get the province codes
		$enums = [
			'PINAR_DEL_RIO', 'LA_HABANA', 'ARTEMISA', 'MAYABEQUE',
			'MATANZAS', 'VILLA_CLARA', 'CIENFUEGOS', 'SANCTI_SPIRITUS',
			'CIEGO_DE_AVILA', 'CAMAGUEY', 'LAS_TUNAS', 'HOLGUIN',
			'GRANMA', 'SANTIAGO_DE_CUBA', 'GUANTANAMO', 'ISLA_DE_LA_JUVENTUD'
		];

		// save country as Cuba
		$this->update("country='CU', usstate=NULL", $request->person->id);

		// update the value on the database
		$this->updateEnum($request->query, $enums, '', 'province', $request->person->id);
	}

	/**
	 * Subservice PAIS
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _pais (Request $request, Response $response)
	{
		// get the list of countries
		$countries = Connection::query("SELECT code, es AS name FROM countries ORDER BY code");
		$country = empty($request->params[0]) ? trim($request->params[1]) : $request->params[0];
		$country_original = $country;

		// do not let empty countries
		if (empty($country)) return;
		$country = strtolower($country);
		$country_original = strtolower($country_original);

		// setup country aliases and typos
		if($country == "us" || $country == "usa" || $country == "united states") $country = "estados unidos";
		if($country == "cu" || $country == "kuba" || $country == "cuva") $country = "cuba";

		// get the country to update
		$max = 0;
		$selectedCountry = null;
		foreach ($countries as $c) {
			// check percentage similarity
			$percent = 0;

			similar_text($country, strtolower($c->name), $percent);
			// select the country with greater similarity
			if ($max < $percent && $percent > 90) {
				$max = $percent;
				$selectedCountry = $c;
			}

			// select by code
			$code = strtolower($c->code);
			if ($code == $country || $code == $country_original) {
				$selectedCountry = $c;
				break;
			}
		}

		// if not country was selected, do nothing
		if (is_null($selectedCountry)) return;

		// update country and return empty response
		$this->update("country='{$selectedCountry->code}'", $request->person->id);
	}

	/**
	 * Subservice CIUDAD
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _ciudad (Request $request, Response $response)
	{
		$this->updateEscape('city', $request->query, $request->person->id, 100);
	}

	/**
	 * Subservice SEXO
	 *
	 * @author salvipascual
	 * @param Request $request
	 * @param Response $response
	 */
	public function _sexo (Request $request, Response $response)
	{
		// for hombre/mujer
		if(strtoupper($request->query) == "HOMBRE") $gender = "M";
		elseif(strtoupper($request->query) == "MUJER") $gender = "F";

		// for Masculino/Femenino
		else $gender = strtoupper($request->query[0]);

		// update the value on the database
		$this->updateEnum($gender, ['F','M'], '', 'gender', $request->person->id);
	}

	/**
	 * Subservice NIVEL
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _nivel (Request $request, Response $response)
	{
		// list of possible values
		$enums = ['PRIMARIO','SECUNDARIO','TECNICO','UNIVERSITARIO','POSTGRADUADO','DOCTORADO','OTRO'];

		// update the value on the database
		$this->updateEnum($request->query, $enums, 'OTRO', 'highest_school_level', $request->person->id);
	}

	/**
	 * Subservice ESTADO CIVIL
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _estadocivil (Request $request, Response $response)
	{
		// list of possible values
		$enums = ['SOLTERO','SALIENDO','COMPROMETIDO','CASADO','OTRO'];
	
		// update the value on the database
		$this->updateEnum(strtoupper($request->query), $enums, 'OTRO', 'marital_status', $request->person->id);	
	}

	/**
	 * Subservice PELO
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _pelo (Request $request, Response $response)
	{
		// list of possible values
		$enums = ['TRIGUENO','CASTANO','RUBIO','NEGRO','ROJO','BLANCO','OTRO'];

		// update the value on the database
		$this->updateEnum($request->query, $enums, 'OTRO', 'hair', $request->person->id);
	}

	/**
	 * Subservice OJOS
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _ojos (Request $request, Response $response)
	{
		// list of possible values
		$enums = ['NEGRO','CARMELITA','VERDE','AZUL','AVELLANA','OTRO'];

		// update the value on the database
		$this->updateEnum($request->query, $enums, 'OTRO', 'eyes', $request->person->id);
	}

	/**
	 * Subservice CUERPO
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _cuerpo (Request $request, Response $response)
	{
		// list of possible values
		$enums = ['DELGADO', 'MEDIO', 'EXTRA', 'ATLETICO'];

		// update the value on the database
		$this->updateEnum($request->query, $enums, '', 'body_type', $request->person->id);
	}

	/**
	 * Subservice INTERESES
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _intereses (Request $request, Response $response)
	{
		$this->updateEscape('interests', $request->query, $request->person->id, 1000);
	}

	/**
	 * Subservice FOTO
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _foto ($request)
	{
		// is the image passed in the subject? (web only)
		if(file_exists($request->query)) $request->attachments = [$request->query];

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
			Utils::optimizeImage($filePath);

			// make the changes in the database
			$this->update("picture='$fileName'", $request->person->id);
			break;
		}

	}

	/**
	 * Subservice FOTO
	 *
	 * @param Request $request
	 * @param Response $response
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
			Utils::optimizeImage($filePath);

			// make the changes in the database
			$this->update("extra_pictures='$pics'", $request->person->id);
			break;
		}

		return $this->_editar($request);
	}

	/**
	 * Subservice ORIENTACION SEXUAL
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _orientacion (Request $request, Response $response)
	{
		// list of possible values
		$enums = ['HETERO','HOMO','BI'];

		// update the value on the database
		$this->updateEnum($request->query, $enums, '', 'sexual_orientation', $request->person->id);
	}

	/**
	 * Subservice orientacion for Piropazo
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _orientacionbusco (Request $request, Response $response)
	{
		// get the person's gender
		$perfil = Utils::getPerson($request->person->id);
		if(empty($perfil->gender)) return;

		// get sexual orientation
		$searchFor = strtoupper($request->query);
		if($searchFor == "AMBOS") $query = "BI";
		elseif($perfil->gender == "M" && $searchFor == "MUJERES") $query = "HETERO";
		elseif($perfil->gender == "F" && $searchFor == "HOMBRES") $query = "HETERO";
		else $query = "HOMO";

		// save new orientacion
		$request->query = $query;
		return $this->_orientacion($request);
	}

	/**
	 * Subservice piel
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _piel (Request $request, Response $response)
	{
		// list of possible values
		$enums = ['NEGRO','BLANCO','MESTIZO','OTRO'];

		// update the value on the database
		$this->updateEnum($request->query, $enums, '', 'skin', $request->person->id);
	}

	/**
	 * Show the edit mode template
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _editar (Request $request, Response $response)
	{
		// get the person to edit profile
		$person = Social::prepareUserProfile($request->person);

		// make the person's text readable
		$person->province = str_replace("_", " ", $person->province);
		if ($person->gender == 'M') $person->gender = "Masculino";
		if ($person->gender == 'F') $person->gender = "Femenino";
		$person->country_name = Utils::getCountryNameByCode($person->country);
		$person->usstate_name = Utils::getStateNameByCode($person->usstate);
		$person->interests = count($person->interests);
		$image = $person->picture ? [$person->picture_internal] : [];

		// add the list of origins and years
		$person->origins = implode(",", $this->origins);
		$person->years = implode(",", array_reverse(range(date('Y')-90, date('Y')-10)));

		// prepare response for the view
		$response->setTemplate('profile_edit.ejs', ["person"=>$person], $image);
	}

	/**
	 * Show the form of where you hear about the app
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _origen (Request $request, Response $response)
	{
		// get the person to add origin
		$person = Utils::getPerson($request->person->id);
		if (empty($person)) return;

		$response->setTemplate('origin.ejs', ["person"=>$person, "origins"=>$this->origins]);
	}

	/**
	 * Subservice ORIGIN
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _origin (Request $request, Response $response)
	{
		// save the value
		$this->updateEnum($request->query, $this->origins, 'OTRO', 'origin', $request->person->id);
	}

	/**
	 * Block an user
	 *
	 * @author ricardo@apretaste.com
	 * @param Request
	 */
	public function _bloquear(Request $request, Response $response){
		$person = Utils::getPersonFromUsername($request->input->data->username);
		$fromEmail = $request->person->email;
		if($person){
			$r = Connection::query("
				SELECT *
				FROM `relations`
				WHERE user1 = '$fromEmail'
				AND user2 = '$person->email'");

			if (isset($r[0])) Connection::query("
				UPDATE `relations` SET confirmed=1
				WHERE user1='$fromEmail'
				AND user2='$person->email' AND `type`='blocked'");
			else Connection::query("
				INSERT INTO `relations`(user1,user2,`type`,confirmed)
				VALUES('$fromEmail','$person->email','blocked',1)");
		}
		return $this->_main($request, $response);
	}

	/**
	 * unlock an user
	 *
	 * @author ricardo@apretaste.com
	 * @param Request
	 */
	public function _desbloquear(Request $request, Response $response)
	{
		$person = Utils::getPersonFromUsername($request->input->data->username);
		$fromEmail = $request->person->email;

		if($person){
			Connection::query("
				UPDATE relations SET confirmed=0
				WHERE user1='$fromEmail'
				AND user2='$person->email' AND `type`='blocked'");
		}
		return $this->_main($request, $response);
	}

	/**
	 * Display your relations
	 *
	 * @author kuma
	 * @version 1.0
	 * @param Request $request
	 * @param Response $response
	 */
	public function _relaciones(Request $request, Response $response)
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
			$relations[$k]->who = Utils::getPerson($v->who);
		}

		// send relations
		if (isset($relations[0])) {
			$response->setTemplate('relations.tpl', ['relations' => $relations]);

		}
		$response->createFromText('Usted no tiene amistades. Encuentre amistades en la Pizarra o otros servicios sociales');
	}

	/**
	 * Change all params at the same time
	 *
	 * @author salvipascual
	 * @version 1.0
	 * @param Request $request
	 * @param Response $response
	 */
	public function _bulk(Request $request, Response $response)
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
				$req->person->id = $request->person->id;
				$req->subject = "PERFIL $key $value";
				$req->service = "PERFIL";
				$req->subservice = $key;
				$req->fromBulk = true;
				$req->query = $value;
				$req->attachments = $request->attachments;
				$function = "_$key";
				if (method_exists($this, $function))
					$this->$function($req);
			}
		}

	}

	/**
	 * Change state if within the US
	 * AL,AK,AS,AZ,AR,CA,CO,CT,DE,DC,FL,GA,GU,HI,ID,IL,IN,IA,KS,KY,LA,ME,MD,MH,MA,MI,FM,MN,MS,MO,MT,NE,NV,NH,NJ,NM,NY,NC,ND,MP,OH,OK,OR,PW,PA,PR,RI,SC,SD,TN,TX,UT,VT,VA,VI,WA,WV,WI,WY
	 *
	 * @author salvipascual
	 * @version 1.0
	 * @param Request $request
	 * @param Response $response
	 */
	public function _usstate(Request $request, Response $response)
	{
		// list of US states
		$state = strtoupper($request->query);
		$states = array("AL","AK","AS","AZ","AR","CA","CO","CT","DE","DC","FL","GA","GU","HI","ID","IL","IN","IA","KS","KY","LA","ME","MD","MH","MA","MI","FM","MN","MS","MO","MT","NE","NV","NH","NJ","NM","NY","NC","ND","MP","OH","OK","OR","PW","PA","PR","RI","SC","SD","TN","TX","UT","VT","VA","VI","WA","WV","WI","WY");

		// save US state
		if(in_array($state, $states)) $this->update("usstate='$state', country='US', province=NULL", $request->person->id);
	}

	/**
	 * Change language
	 *
	 * @author salvipascual
	 * @version 1.0
	 * @param Request $request
	 * @param Response $response
	 */
	public function _lang(Request $request, Response $response)
	{
		// get the values from the post
		$email = $request->email;
		$lang = $request->query;

		// check if the language exist
		$test = Connection::query("SELECT * FROM languages WHERE code = '$lang'");
		if(empty($test)) return;

		// set the new language for the user
		Connection::query("UPDATE person SET lang='$lang' WHERE email='$email'");
	}

	/**
	 * Get the global info for the General app
	 *
	 * @author salvipascual
	 * @api
	 * @version 1.0
	 * @param Request $request
	 * @param Response $response
	 */
	public function _status(Request $request, Response $response)
	{
		// get data for the app
		$res = Utils::getExternalAppData($request->email, $request->query);

		// respond back to the API
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
	 * @param Response $response
	 */
	public function _imagen(Request $request, Response $response)
	{
		// list of possible values
		$quality = strtoupper($request->query);
		$enums = ['ORIGINAL', 'REDUCIDA', 'SIN_IMAGEN'];

		// save the image quality
		if(in_array($quality, $enums)) {
			$this->update("img_quality='$quality'", $request->person->id);
		}

	}

	/**
	 * Update a profile
	 *
	 * @param String $sqlset
	 * @param String $userId
	 */
	private function update($sqlset, $userId)
	{
		if(empty($userId)) return;

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
		if(empty($value)) return;

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