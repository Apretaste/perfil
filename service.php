<?php

use Apretaste\Model\Person;

class Service
{
	private $origins = ["Amigo en Cuba", "Familia Afuera", "Referido", "El Paquete", "Revolico", "Casa de Apps", "Facebook", "Internet", "La Calle", "Prensa Independiente", "Prensa Cubana", "Otro"];

	/**
	 * Display your profile
	 *
	 * @param Request  $request
	 * @param Response $response
	 * @return \Response|void
	 * @throws \Exception
	 */
	public function _main(Request $request, Response $response)
	{
		// get the email or the username for the profile
		$data = $request->input->data;
		$content = new stdClass();

		if (!empty($data->username) && $data->username != $request->person->username) {
			// get the data of the person requested
			$user = Utils::getPerson($data->username);

			// run powers for amulet SHADOWMODE
			if(Amulets::isActive(Amulets::SHADOWMODE, $user->id)) {
				return $response->setTemplate("message.ejs", [
					"header" => "Shadow-Mode",
					"icon" => "visibility_off",
					"text" => "La magia oscura de un amuleto rodea este perfil y te impide verlo. Por mucho que intentes romperlo, el hechizo del druida es poderoso."
				]);
			}

			// check if the person exist. If not, message the requestor
			if (!$user) {
				return $response->setTemplate("message.ejs", [
					"header" => "El perfil no existe",
					"icon" => "sentiment_very_dissatisfied",
					"text" => "Lo sentimos, pero el perfil que usted busca no pudo ser encontrado. Puede que el nombre de usuario halla cambiado o la persona halla salido de la app."
				]);
			}

			// prepare the profile for the person requested
			$profile = Social::prepareUserProfile($user);
			$ownProfile = false;

			// check if current user blocked the user to lookup, or is blocked by
			$blocks = Social::isBlocked($request->person->id, $user->id);
			if ($blocks->blocked || $blocks->blockedByMe) {
				return $response->setTemplate("message.ejs", [
					"header" => "Perfil bloqueado",
					"icon" => "sentiment_very_dissatisfied",
					"text" => "Esta persona le ha bloqueado, o usted ha bloqueado a esta persona, por lo tanto no puede revisar su perfil."
				]);
			}

			// run powers for amulet DETECTIVE
			if(Amulets::isActive(Amulets::DETECTIVE, $profile->id)) {
				$msg = "Los poderes del amuleto del Druida te avisan: @{$request->person->username} está revisando tu perfil";
				Utils::addNotification($profile->id, $msg, '{command:"PERFIL", data:{username:"@{$request->person->username}"}}', 'pageview');
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

	/**
	 * Edit your profile
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _editar(Request $request, Response $response)
	{
		$pathToService = Utils::getPathToService($response->serviceName);
		$images = ["$pathToService/images/avatars.png"];

		$response->setTemplate('edit.ejs', ['profile' => $request->person], $images);
	}

	/**
	 * Get the list of levels
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _niveles(Request $request, Response $response)
	{
		$response->setTemplate('levels.ejs', ['experience' => $request->person->experience], $this->gemsImages());
	}

	/**
	 * Get ways of gaining experience
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _experiencia(Request $request, Response $response)
	{
		// get the experience leve
		$experience = Connection::query("
			SELECT description, value
			FROM person_experience_rules
			WHERE active = 1
			ORDER BY value");

		// send data to the view
		$response->setCache();
		$response->setTemplate('experience.ejs', ['experience'=>$experience]);
	}

	/**
	 * Edit your avatar
	 *
	 * @param Request $request
	 * @param Response $response
	 */
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

		Challenges::complete("update-profile-picture", $request->person->id);
	}

	/**
	 * Show the form of where you hear about the app
	 *
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @throws \Exception
	 */
	public function _origen(Request $request, Response $response)
	{
		// get the person to add origin
		$content = new stdClass();
		$content->origin = $request->person->origin;
		$content->origins = $this->origins;

		$response->setTemplate('origin.ejs', $content);

		Challenges::complete("where-found-apretaste", $request->person->id);
	}

	/**
	 * Block an user
	 *
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @throws \Exception
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
	 * @param \Response $response
	 *
	 * @throws \Exception
	 * @author ricardo@apretaste.com
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
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @throws \Exception
	 * @author  salvipascual
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

				if ($key == 'avatar') {
					Challenges::complete('update-profile-picture', $request->person->id);
				}
			}
		}

		// save changes on the database
		if (!empty($pieces)) {
			Connection::query("UPDATE person SET " . implode(",", $pieces) . " WHERE id={$request->person->id}");
		}

		// add the experience if profile is completed
		if ($request->person->completion > 80) {
			Challenges::complete('complete-profile', $request->person->id);
			Level::setExperience('FINISH_PROFILE_FIRST', $request->person->id);
		}
	}

	/**
	 * Get the images for the gems
	 *
	 * @param Request $request
	 * @param Response $response
	 * @version 1.0
	 */
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
