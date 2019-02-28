<?php

use \Phalcon\DI\FactoryDefault;

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
			$user = Utils::getPerson($data->username);
			// check if the person exist. If not, message the requestor
			if(!$user){
				$content->username = $data->username;
				$response->setTemplate("not_found.ejs",$content);
				return;
			}

			$profile = Social::prepareUserProfile($user);
			$tickets=0;
			$ownProfile = false;

			// check if current user blocked the user to lookup, or is blocked by
			$blocks = Social::isBlocked($request->person->id, $user->id);
			if ($blocks->blocked || $blocks->blockedByMe){
				$content->username = $user->username;
				$content->blocks = $blocks;
				$response->setTemplate("blocked.ejs", $content);
				return;
			}
		}
		else{
			$profile = $request->person;
			$ownProfile = true;
			$email = Utils::getPerson($request->person->id)->email;
			// and get the number of tickets for the raffle
			// @TODO change email with id
			$tickets = Connection::query("SELECT count(ticket_id) as tickets FROM ticket WHERE raffle_id is NULL AND email = '{$email}'")[0]->tickets;
		}

		// pass profile image to the response
		$image = [];
		if ($profile->picture) $image[] = $profile->picture;

		foreach ($profile->extra_pictures as $key => $picture){
			$image[] = $picture;
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
		if(!isset($request->input->data->picture)) return;
		$picture = $request->input->data->picture;

		// get the image name and path
		$wwwroot = FactoryDefault::getDefault()->get('path')['root'];
		$fileName = Utils::generateRandomHash();
		$filePath = "$wwwroot/public/profile/$fileName.jpg";

		// save the optimized image on the user folder
		file_put_contents($filePath, base64_decode($picture));
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
		$person = $request->person;

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
	 * Block an user
	 *
	 * @author ricardo@apretaste.com
	 * @param Request
	 */
	public function _bloquear(Request $request, Response $response){
		$person = Utils::getPerson($request->input->data->username);
		$fromId = $request->person->id;
		if($person){
			$r = Connection::query("
				SELECT *
				FROM `relations`
				WHERE user1 = '$fromId'
				AND user2 = '$person->id'");
			if (isset($r[0])) Connection::query("
				UPDATE `relations` SET confirmed=1
				WHERE user1='$fromId'
				AND user2='$person->id' AND `type`='blocked'");
			else Connection::query("
				INSERT INTO `relations`(user1,user2,`type`,confirmed)
				VALUES('$fromId','$person->id','blocked',1)");
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
		$person = Utils::getPerson($request->input->data->username);
		$fromId = $request->person->id;
		if($person){
			Connection::query("
				UPDATE relations SET confirmed=0
				WHERE user1='$fromId'
				AND user2='$person->id' AND `type`='blocked'");
		}
		return $this->_main($request, $response);
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
}