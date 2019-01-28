<?php

class Service
{
	private $origins = [
		"Amigo en Cuba", "Familia Afuera", "Referido", 
		"El Paquete", "Revolico", "Casa de Apps", "Facebook", "Internet",
		"La Calle", "Prensa Independiente", "Prensa Cubana", "Otro"];

	/**
	 * Display your profile
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _main (Request $request, Response $response)
	{
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
				return;
			}

			$tickets=0;
			$ownProfile = false;

			// check if current user blocked the user to lookup, or is blocked by
			$blocks = Social::isBlocked($request->person->email,$user->email);
			$user->blocked = $blocks->blocked;
			$user->blockedByMe = $blocks->blockedByMe;
			$content->username = $user->username;
			if ($user->blocked){
				$response->setTemplate("blocked.ejs",$content);
				return;
			}
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
		if ($profile->picture) $image[]=$profile->picture;

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
	 * Subservice FOTO
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _foto (Request $request, Response $response)
	{
		// do not allow empty files
		$content = $request->input->data->content;
		if (empty($content)) return $response;

		// get the image name and path
		$di = \Phalcon\DI\FactoryDefault::getDefault();
		$wwwroot = $di->get('path')['root'];
		$fileName = Utils::generateRandomHash();
		$filePath = "$wwwroot/public/profile/$fileName.jpg";

		// save the optimized image on the user folder
		file_put_contents($filePath, base64_decode($content));
		Utils::optimizeImage($filePath);

		// save changes on the database 
		Connection::query("UPDATE person SET picture='$fileName' WHERE id={$request->person->id}");
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
		$image = $person->picture ? [$person->picture] : [];
		$person->years = implode(",", array_reverse(range(date('Y')-90, date('Y')-10)));

		// create the info for the view
		$content = new stdClass();
		$content->profile = $person;
		$content->origins = $this->origins;
		$content->origin = $person->origin;

		// prepare response for the view
		$response->setTemplate('profile_edit.ejs', $content, $image);
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
		$content = new stdClass();
		$content->origin = $request->person->origin;
		$content->origins = $this->origins;

		$response->setTemplate('origin.ejs', $content);
	}

	/**
	 * Update your profile 
	 *
	 * @author salvipascual
	 * @version 1.0
	 * @param Request $request
	 * @param Response $response
	 */
	public function _update(Request $request, Response $response)
	{
		// posible fields to update in the database
		$fields = ['username','first_name','middle_name','last_name','mother_name','year_of_birth','date_of_birth','gender','phone','cellphone','eyes','skin','body_type','hair','province','city','highest_school_level','occupation','marital_status','interests','about_me','lang','mail_list','picture','sexual_orientation','religion','origin','country','usstate','img_quality'];

		// clean, shorten and lowercase the username, if passed
		if( ! empty($request->input->data->username)) {
			$username = preg_replace("/[^a-zA-Z0-9]+/", "", $request->input->data->username);
			$request->input->data->username = strtolower(substr($username, 0, 15));
		}

		// get the JSON with the bulk
		$pieces = [];
		foreach ($request->input->data as $key=>$value) {
			if(in_array($key, $fields)) $pieces[] = "$key='$value'";
		}

		// save changes on the database 
		Connection::query("UPDATE person SET " . implode(",", $pieces) . " WHERE id={$request->person->id}");
	}

	// /**
	//  * Block an user
	//  *
	//  * @author ricardo@apretaste.com
	//  * @param Request
	//  */
	// public function _bloquear(Request $request, Response $response)
	// {
	// 	$person = Utils::getPersonFromUsername($request->input->data->username);
	// 	$fromEmail = $request->person->email;
	// 	if($person){
	// 		$r = Connection::query("
	// 			SELECT *
	// 			FROM `relations`
	// 			WHERE user1 = '$fromEmail'
	// 			AND user2 = '$person->email'");

	// 		if (isset($r[0])) Connection::query("
	// 			UPDATE `relations` SET confirmed=1
	// 			WHERE user1='$fromEmail'
	// 			AND user2='$person->email' AND `type`='blocked'");
	// 		else Connection::query("
	// 			INSERT INTO `relations`(user1,user2,`type`,confirmed)
	// 			VALUES('$fromEmail','$person->email','blocked',1)");
	// 	}
	// 	return $this->_main($request, $response);
	// }

	// /**
	//  * unlock an user
	//  *
	//  * @author ricardo@apretaste.com
	//  * @param Request
	//  */
	// public function _desbloquear(Request $request, Response $response)
	// {
	// 	$person = Utils::getPersonFromUsername($request->input->data->username);
	// 	$fromEmail = $request->person->email;

	// 	if($person){
	// 		Connection::query("
	// 			UPDATE relations SET confirmed=0
	// 			WHERE user1='$fromEmail'
	// 			AND user2='$person->email' AND `type`='blocked'");
	// 	}
	// 	return $this->_main($request, $response);
	// }


	// /**
	//  * Change all params at the same time
	//  *
	//  * @author salvipascual
	//  * @version 1.0
	//  * @param Request $request
	//  * @param Response $response
	//  */
	// public function _bulk(Request $request, Response $response)
	// {
	// 	// get the JSON with the bulk
	// 	$json = $request->input->data;

	// 	// if the method exist, call it
	// 	if (is_object($json)) foreach ($json as $key=>$value){
	// 		$key = strtolower($key);
	// 		if(method_exists($this, $key)) $this->$key($request,$response);
	// 	}
	// }

	// //FROM HERE ALL FUNCTIONS TO UPDATE THE PROFILE IN THE DB

	// private function username (Request $request, Response $response)
	// {
	// 	// clean, shorten and lowercase the text
	// 	$username = preg_replace("/[^a-zA-Z0-9]+/", "", $request->input->data->username);
	// 	$username = strtolower(substr($username, 0, 15));

	// 	// check if the username not exist, else if not belong to the user, recreate it
	// 	$exist = Connection::query("SELECT id AS exist FROM person WHERE username='$username'");
	// 	if(!empty($exist) && $exist[0]->id!=$request->person->id) $username = Utils::usernameFromEmail($username);

	// 	// update the username in the database
	// 	$this->updateEscape('username', $username, $request->person->id, 15);
	// }

	// private function first_name(Request $request, Response $response)
	// {
	// 	$query = " first_name='{$request->input->data->first_name}'";
	// 	$this->update($query, $request->person->id);
	// }

	// private function middle_name(Request $request, Response $response)
	// {
	// 	$query = " middle_name='{$request->input->data->middle_name}'";
	// 	$this->update($query, $request->person->id);
	// }

	// private function last_name(Request $request, Response $response)
	// {
	// 	$query = " last_name='{$request->input->data->last_name}'";
	// 	$this->update($query, $request->person->id);
	// }

	// private function mother_name(Request $request, Response $response)
	// {
	// 	$query = " mother_name='{$request->input->data->mother_name}'";
	// 	$this->update($query, $request->person->id);
	// }

	// private function phone(Request $request, Response $response)
	// {
	// 	// remove all non-numeric characters from the phone
	// 	$phone = preg_replace('/[^0-9.]+/', '', $request->input->data->phone);

	// 	// is cell or not?
	// 	$field = 'phone';
	// 	if(substr($phone, 0, 1) == '5') $field = 'cellphone';

	// 	// update phone
	// 	$this->updateEscape($field, $phone, $request->person->id, 10);
	// }

	// private function date_of_birth(Request $request, Response $response)
	// {
	// 	// get the params for old versions of the app
	// 	$pieces = explode("/", $request->input->data->date_of_birth);

	// 	// get the date passed
	// 	$day = $pieces[0];
	// 	$month = $pieces[1];
	// 	$year = $pieces[2];

	// 	// get possible birth years ranges
	// 	$yearMin = date('Y') - 90;
	// 	$yearMax = date('Y') - 10;

	// 	// do not save dates out of range
	// 	if($year < $yearMin || $year > $yearMax) return;
	// 	if($month < 1 || $month > 12) return;
	// 	if($day < 1 || $day > 31) return;

	// 	// save the date in the database
	// 	$this->update("year_of_birth='$year'", $request->person->id);
	// 	$this->update("date_of_birth='$year-$month-$day'", $request->person->id);
	// }

	// private function description(Request $request, Response $response)
	// {
	// 	// do not allow empty or long descriptions
	// 	$description = Connection::escape($request->input->data->description, 250);
	// 	if (strlen($description) < 50) return;

	// 	// set the description
	// 	$this->update("about_me='$description'", $request->person->id);
	// }

	// private function occupation(Request $request, Response $response)
	// {
	// 	$this->updateEscape('occupation', $request->input->data->occupation, $request->person->id, 50);
	// }

	// private function religion(Request $request, Response $response)
	// {
	// 	// list of religions
	// 	$enums = [
	// 		'ATEISMO',
	// 		'SECULARISMO',
	// 		'AGNOSTICISMO',
	// 		'ISLAM',
	// 		'JUDAISTA',
	// 		'ABAKUA',
	// 		'SANTERO',
	// 		'YORUBA',
	// 		'BUDISMO',
	// 		'CATOLICISMO',
	// 		'PROTESTANTE',
	// 		'CRISTIANISMO',
	// 		'OTRA'];

	// 	// update the value on the database
	// 	$this->updateEnum($request->input->data->religion, $enums, 'OTRA', 'religion', $request->person->id);
	// }

	// private function province(Request $request, Response $response)
	// {
	// 	// get the province codes
	// 	$enums = [
	// 		'PINAR_DEL_RIO', 'LA_HABANA', 'ARTEMISA', 'MAYABEQUE',
	// 		'MATANZAS', 'VILLA_CLARA', 'CIENFUEGOS', 'SANCTI_SPIRITUS',
	// 		'CIEGO_DE_AVILA', 'CAMAGUEY', 'LAS_TUNAS', 'HOLGUIN',
	// 		'GRANMA', 'SANTIAGO_DE_CUBA', 'GUANTANAMO', 'ISLA_DE_LA_JUVENTUD'
	// 	];

	// 	// save country as Cuba
	// 	$this->update("country='CU', usstate=NULL", $request->person->id);

	// 	// update the value on the database
	// 	$this->updateEnum($request->input->data->province, $enums, '', 'province', $request->person->id);
	// }

	// private function country(Request $request, Response $response)
	// {
	// 	// get the list of countries
	// 	$countries = Connection::query("SELECT code, es AS name FROM countries ORDER BY code");
	// 	$country = $request->input->data->country;
	// 	$country_original = $country;

	// 	// do not let empty countries
	// 	if (empty($country)) return;
	// 	$country = strtolower($country);
	// 	$country_original = strtolower($country_original);

	// 	// setup country aliases and typos
	// 	if($country == "us" || $country == "usa" || $country == "united states") $country = "estados unidos";
	// 	if($country == "cu" || $country == "kuba" || $country == "cuva") $country = "cuba";

	// 	// get the country to update
	// 	$max = 0;
	// 	$selectedCountry = null;
	// 	foreach ($countries as $c) {
	// 		// check percentage similarity
	// 		$percent = 0;

	// 		similar_text($country, strtolower($c->name), $percent);
	// 		// select the country with greater similarity
	// 		if ($max < $percent && $percent > 90) {
	// 			$max = $percent;
	// 			$selectedCountry = $c;
	// 		}

	// 		// select by code
	// 		$code = strtolower($c->code);
	// 		if ($code == $country || $code == $country_original) {
	// 			$selectedCountry = $c;
	// 			break;
	// 		}
	// 	}

	// 	// if not country was selected, do nothing
	// 	if (is_null($selectedCountry)) return;

	// 	// update country and return empty response
	// 	$this->update("country='{$selectedCountry->code}'", $request->person->id);
	// }

	// private function city(Request $request, Response $response)
	// {
	// 	$this->updateEscape('city', $request->input->data->city, $request->person->id, 100);
	// }

	// private function gender(Request $request, Response $response)
	// {
	// 	// for hombre/mujer
	// 	$gender = strtoupper($request->input->data->gender);
	// 	if($gender == "HOMBRE") $gender = "M";
	// 	elseif($gender == "MUJER") $gender = "F";

	// 	// update the value on the database
	// 	$this->updateEnum($gender, ['F','M'], '', 'gender', $request->person->id);
	// }

	// private function highest_school_level (Request $request, Response $response)
	// {
	// 	// list of possible values
	// 	$enums = ['PRIMARIO','SECUNDARIO','TECNICO','UNIVERSITARIO','POSTGRADUADO','DOCTORADO','OTRO'];

	// 	// update the value on the database
	// 	$this->updateEnum($request->input->data->highest_school_level, $enums, 'OTRO', 'highest_school_level', $request->person->id);
	// }

	// private function marital_status (Request $request, Response $response)
	// {
	// 	// list of possible values
	// 	$enums = ['SOLTERO','SALIENDO','COMPROMETIDO','CASADO','OTRO'];
	
	// 	// update the value on the database
	// 	$this->updateEnum(strtoupper($request->input->data->marital_status), $enums, 'OTRO', 'marital_status', $request->person->id);	
	// }

	// private function hair (Request $request, Response $response)
	// {
	// 	// list of possible values
	// 	$enums = ['TRIGUENO','CASTANO','RUBIO','NEGRO','ROJO','BLANCO','OTRO'];

	// 	// update the value on the database
	// 	$this->updateEnum($request->input->data->hair, $enums, 'OTRO', 'hair', $request->person->id);
	// }

	// private function eyes (Request $request, Response $response)
	// {
	// 	// list of possible values
	// 	$enums = ['NEGRO','CARMELITA','VERDE','AZUL','AVELLANA','OTRO'];

	// 	// update the value on the database
	// 	$this->updateEnum($request->input->data->eyes, $enums, 'OTRO', 'eyes', $request->person->id);
	// }

	// private function body_type (Request $request, Response $response)
	// {
	// 	// list of possible values
	// 	$enums = ['DELGADO', 'MEDIO', 'EXTRA', 'ATLETICO'];

	// 	// update the value on the database
	// 	$this->updateEnum($request->input->data->body_type, $enums, '', 'body_type', $request->person->id);
	// }

	// private function interests (Request $request, Response $response)
	// {
	// 	$interests = [];
	// 	foreach($request->input->data->interests as $interest) $interests[] = $interest->tag;
	// 	$interests = implode(',',$interests);
	// 	$this->updateEscape('interests', $interests, $request->person->id, 1000);
	// }

	// private function sexual_orientation (Request $request, Response $response)
	// {
	// 	// list of possible values
	// 	$enums = ['HETERO','HOMO','BI'];

	// 	// update the value on the database
	// 	$this->updateEnum($request->input->data->sexual_orientation, $enums, '', 'sexual_orientation', $request->person->id);
	// }

	// private function skin (Request $request, Response $response)
	// {
	// 	// list of possible values
	// 	$enums = ['NEGRO','BLANCO','MESTIZO','OTRO'];

	// 	// update the value on the database
	// 	$this->updateEnum($request->input->data->skin, $enums, '', 'skin', $request->person->id);
	// }

	// private function origin (Request $request, Response $response)
	// {
	// 	// save the value
	// 	$this->updateEnum($request->input->data->origin, $this->origins, 'OTRO', 'origin', $request->person->id);
	// }

	// private function usstate(Request $request, Response $response)
	// {
	// 	// list of US states
	// 	$state = strtoupper($request->input->data->usstate);
	// 	$states = array("AL","AK","AS","AZ","AR","CA","CO","CT","DE","DC","FL","GA","GU","HI","ID","IL","IN","IA","KS","KY","LA","ME","MD","MH","MA","MI","FM","MN","MS","MO","MT","NE","NV","NH","NJ","NM","NY","NC","ND","MP","OH","OK","OR","PW","PA","PR","RI","SC","SD","TN","TX","UT","VT","VA","VI","WA","WV","WI","WY");

	// 	// save US state
	// 	if(in_array($state, $states)) $this->update("usstate='$state', country='US', province=NULL", $request->person->id);
	// }

	// private function lang(Request $request, Response $response)
	// {
	// 	// get the values from the post
	// 	$email = $request->email;
	// 	$lang = $request->input->data->lang;

	// 	// check if the language exist
	// 	$test = Connection::query("SELECT * FROM languages WHERE code = '$lang'");
	// 	if(empty($test)) return;

	// 	// set the new language for the user
	// 	Connection::query("UPDATE person SET lang='$lang' WHERE email='$email'");
	// }

	// private function img_quality(Request $request, Response $response)
	// {
	// 	// list of possible values
	// 	$quality = strtoupper($request->input->data->quality);
	// 	$enums = ['ORIGINAL', 'REDUCIDA', 'SIN_IMAGEN'];

	// 	// save the image quality
	// 	if(in_array($quality, $enums)) {
	// 		$this->update("img_quality='$quality'", $request->person->id);
	// 	}

	// }

	// /**
	//  * Update a profile
	//  *
	//  * @param String $sqlset
	//  * @param String $userId
	//  */
	// private function update($sqlset, $userId){
	// 	if(empty($userId)) return;

	// 	Connection::query("
	// 		UPDATE person 
	// 		SET $sqlset, last_update_date=CURRENT_TIMESTAMP, updated_by_user=1 
	// 		WHERE id='$userId'");
	// }

	// /**
	//  * Update a profile scaping string size
	//  *
	//  * @param String $field : field name in the database
	//  * @param String $value : value to save
	//  * @param String $userId
	//  * @param Integer $cut : Max length of the value
	//  */
	// private function updateEscape($field, $value, $userId, $cut=false){
	// 	// escape and cut the string
	// 	$value = Connection::escape($value, $cut);

	// 	// save to the database
	// 	$this->update("$field='$value'", $userId);
	// }

	// /**
	//  * Sub-service utility for simple profile fields
	//  *
	//  * @author salvipascual
	//  * @param String $value : ASUL
	//  * @param Array $enums : ['ROJO', 'VERDE', 'AZUL', 'OTRO'] 
	//  * @param String $default : OTRO
	//  * @param String $field : field name in the database
	//  * @param integer $userId
	//  */
	// private function updateEnum($value, $enums, $default, $field, $userId){
	// 	// do not allow empty responses
	// 	if(empty($value)) return;

	// 	// set initial params
	// 	$value = strtolower($value);
	// 	$selected = null;
	// 	$max = 0;

	// 	// pick the most similar value
	// 	foreach ($enums as $enum) {
	// 		// check percentage similarity
	// 		$percent = 0;
	// 		similar_text($value, strtolower($enum), $percent);

	// 		// select the greatest similar
	// 		if ($max < $percent && $percent > 70) {
	// 			$max = $percent;
	// 			$selected = $enum;
	// 		}
	// 	}

	// 	// if nothing is selected, get the default value
	// 	if( ! $selected) $selected = strtolower($default);

	// 	// if empty, make the field NULL
	// 	$query = empty($selected) ? "$field=NULL" : "$field='$selected'";

	// 	// update the table
	// 	$this->update($query, $userId);
	// }
}