<?php

class Service
{
	private $origins = ["Amigo en Cuba", "Familia Afuera", "Referido", "El Paquete", "Revolico", "Casa de Apps", "Facebook", "Internet", "La Calle", "Prensa Independiente", "Prensa Cubana", "Otro"];

	/**
	 * Display your profile
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _main(Request $request, Response $response)
	{
		// get the email or the username for the profile
		$data = $request->input->data;
		$content = new stdClass();

		if (!empty($data->username) && $data->username != $request->person->username) {
			$user = Utils::getPerson($data->username);

			// check if the person exist. If not, message the requestor
			if (!$user) {
				$content->username = $data->username;
				$response->setTemplate("not_found.ejs", $content);
				return;
			}

			$profile = Social::prepareUserProfile($user);
			$ownProfile = false;

			// check if current user blocked the user to lookup, or is blocked by
			$blocks = Social::isBlocked($request->person->id, $user->id);
			if ($blocks->blocked || $blocks->blockedByMe) {
				$content->username = $user->username;
				$content->blocks = $blocks;
				return $response->setTemplate("blocked.ejs", $content);
			}
		} else {
			$profile = $request->person;
			$ownProfile = true;
		}

		unset($profile->credit, $profile->tickets, $profile->cellphone);
		Social::getTags($profile);

		// pass profile image to the response
		$images = [];
		if ($profile->picture) {
			$images[] = $profile->picture;
		}

		// pass variables to the template
		$content->profile = $profile;
		$content->ownProfile = $ownProfile;

		$pathToService = Utils::getPathToService($response->serviceName);
		$images = ["$pathToService/images/avatars.png"];

		// create a new Response object and input the template and the content
		if (!$ownProfile) {
			$response->setCache(240);
		}
		$response->setTemplate("profile.ejs", $content, $this->gemsImages($images));
	}

	public function _editar(Request $request, Response $response)
	{
		$pathToService = Utils::getPathToService($response->serviceName);
		$images = ["$pathToService/images/avatars.png"];

		$response->setTemplate('edit.ejs', ['profile' => $request->person], $images);
	}

	public function _niveles(Request $request, Response $response)
	{
		$response->setTemplate('levels.ejs', ['experience' => $request->person->experience], $this->gemsImages());
	}

	public function _avatar(Request $request, Response $response)
	{
		$pathToService = Utils::getPathToService($response->serviceName);
		$images = ["$pathToService/images/avatars.png"];

		$response->setTemplate('avatar_select.ejs', [
			'currentAvatar' => $request->person->avatar,
			'currentColor' => $request->person->avatarColor
		], $images);
	}

	/**
	 * Subservice FOTO
	 *
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @throws \Exception
	 */
	public function _foto(Request $request, Response $response)
	{
		// do not allow empty files
		if (!isset($request->input->data->picture)) {
			return;
		}
		$picture = $request->input->data->picture;

		// get the image name and path
		$wwwroot = \Phalcon\DI\FactoryDefault::getDefault()->get('path')['root'];
		$fileName = Utils::generateRandomHash();
		$filePath = "$wwwroot/shared/img/profile/$fileName.jpg";

		// save the optimized image on the user folder
		file_put_contents($filePath, base64_decode($picture));
		Utils::optimizeImage($filePath);

		// save changes on the database
		Connection::query("UPDATE person SET picture='$fileName' WHERE id={$request->person->id}");

		if (isset($request->person->completion) && $request->person->completion > 70) {
			Challenges::complete('complete-profile', $request->person->id);
		}
	}

	/**
	 * Show the form of where you hear about the app
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _origen(Request $request, Response $response)
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
	 * @param Request $request
	 * @param Response $response
	 * @author ricardo@apretaste.com
	 */
	public function _bloquear(Request $request, Response $response)
	{
		$person = Utils::getPerson($request->input->data->username);
		$fromId = $request->person->id;

		if ($person) {
			$r = Connection::query("SELECT * FROM relations WHERE user1='$fromId' AND user2='$person->id'");
			if (isset($r[0])) {
				Connection::query("UPDATE relations SET confirmed=1 WHERE user1='$fromId' AND user2='$person->id' AND `type`='blocked'");
			} else {
				Connection::query("INSERT INTO `relations`(user1,user2,`type`,confirmed) VALUES ('$fromId','$person->id','blocked',1)");
			}
		}

		$this->_main($request, $response);
	}

	/**
	 * unlock an user
	 *
	 * @param Request
	 * @author ricardo@apretaste.com
	 *
	 */
	public function _desbloquear(Request $request, Response $response)
	{
		$person = Utils::getPerson($request->input->data->username);
		$fromId = $request->person->id;
		if ($person) {
			Connection::query("
				UPDATE relations SET confirmed=0
				WHERE user1='$fromId'
				AND user2='$person->id' AND `type`='blocked'");
		}
		$this->_main($request, $response);
	}

	/**
	 * Update your profile
	 *
	 * @param Request $request
	 * @param Response $response
	 * @author salvipascual
	 * @version 1.0
	 */
	public function _update(Request $request, Response $response)
	{
		// posible fields to update in the database
		$fields = ['username', 'first_name', 'middle_name', 'last_name', 'mother_name', 'about_me', 'avatar', 'avatarColor', 'year_of_birth', 'month_of_birth', 'day_of_birth', 'gender', 'cellphone', 'eyes', 'skin', 'body_type', 'hair', 'province', 'city', 'highest_school_level', 'occupation', 'marital_status', 'interests', 'about_me', 'lang', 'mail_list', 'picture', 'sexual_orientation', 'religion', 'origin', 'country', 'usstate', 'img_quality'];

		// clean, shorten and lowercase the username, if passed
		if (!empty($request->input->data->username)) {
			$username = preg_replace("/[^a-zA-Z0-9]+/", "", $request->input->data->username);
			$request->input->data->username = strtolower(substr($username, 0, 15));
			if (Utils::getPerson($username)) {
				Utils::addNotification($request->person->id, "Lo sentimos, el username @$username ya esta siendo usado");
				unset($request->input->data->username);
			}
		}

		// get the JSON with the bulk
		$pieces = [];
		foreach ($request->input->data as $key => $value) {
			// format first_name OR last_name in capital the first letter
			if ($key == "first_name" || $key == "last_name") {
				$value = ucfirst(strtolower($value));
			}

			// format interests as a CVS to be saved
			if ($key == 'interests') {
				$interests = [];
				foreach ($value as $piece) {
					$interests[] = $piece->tag;
				}
				$value = implode(',', $interests);
			}

			// escape dangerous chars in the value passed
			$value = Connection::escape($value);

			// prepare the database query
			if (in_array($key, $fields)) {
				if ($value === null || $value === "") {
					$pieces[] = "$key = null";
				} else {
					$pieces[] = "$key = '$value'";
				}
			}
		}

		// save changes on the database
		if (!empty($pieces)) {
			Connection::query("UPDATE person SET " . implode(",", $pieces) . " WHERE id={$request->person->id}");
		}
	}

	private function gemsImages(array $images = [])
	{
		$gems = ['Zafiro', 'Topacio', 'Rubi', 'Opalo', 'Esmeralda', 'Diamante'];
		$path = Utils::getPathToService('perfil') . '/images/';
		foreach ($gems as $gem) {
			$images[] = $path . $gem . '.png';
		}
		return $images;
	}
}
