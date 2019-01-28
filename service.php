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
			$blocks = Social::isBlocked($request->person->id,$user->id);
			$user->blocked = $blocks->blocked;
			$user->blockedByMe = $blocks->blockedByMe;
			$content->username = $user->username;
			if ($user->blocked){
				$response->setTemplate("blocked.ejs",$content);
				return;
			}
		}
		else{
			$profile = $request->person;
			$ownProfile = true;
			// and get the number of tickets for the raffle
			$tickets = Connection::query("SELECT count(ticket_id) as tickets FROM ticket WHERE raffle_id is NULL AND email = '{$profile->email}'")[0]->tickets;
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