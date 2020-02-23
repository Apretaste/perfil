<?php

use Apretaste\Core;

class Service
{
	private $origins = ["AMIGO_EN_CUBA" => "Amigo en Cuba", "FAMILIA_AFUERA" => "Familia Afuera", "REFERIDO" => "Referido", "PAQUETE" => "El Paquete", "REVOLICO" => "Revolico", "BAJANDA" => "Bajanda", "CASA_DE_APPS" => "Casa de Apps", "FACEBOOK" => "Facebook", "INTERNET" => "Internet", "CALLE" => "La Calle", "PRENSA_INDEPENDIENTE" => "Prensa Independiente", "PRENSA_CUBANA" => "Prensa Cubana", "OTRO" => "Otro"];
	private $avatars = ["apretin", "apretina", "artista", "bandido", "belleza", "chica", "coqueta", "cresta", "deportiva", "dulce", "emo", "oculto", "extranna", "fabulosa", "fuerte", "ganadero", "geek", "genia", "gotica", "gotico", "guapo", "hawaiano", "hippie", "hombre", "atento", "libre", "jefe", "jugadora", "mago", "metalero", "modelo", "moderna", "musico", "nerd", "punk", "punkie", "rap", "rapear", "rapero", "rock", "rockera", "rubia", "rudo", "sencilla", "sencillo", "sennor", "sennorita", "sensei", "surfista", "tablista", "vaquera"];

	/**
	 * Display your profile
	 *
	 * @param Request $request
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
			$profile = Social::prepareUserProfile($user);

			// run powers for amulet DETECTIVE
			if (Amulets::isActive(Amulets::DETECTIVE, $profile->id)) {
				$msg = "Los poderes del amuleto del Druida te avisan: @{$request->person->username} está revisando tu perfil";
				Utils::addNotification($profile->id, $msg, '{command:"PERFIL", data:{username:"@{$request->person->username}"}}', 'pageview');
			}

			// run powers for amulet SHADOWMODE
			if (Amulets::isActive(Amulets::SHADOWMODE, $user->id)) {
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

			$ownProfile = $profile->id === $request->person->id;

			// check if current user blocked the user to lookup, or is blocked by
			$blocks = Social::isBlocked($request->person->id, $user->id);
			if ($blocks->blocked || $blocks->blockedByMe) {
				return $response->setTemplate("message.ejs", [
					"header" => "Perfil bloqueado",
					"icon" => "sentiment_very_dissatisfied",
					"text" => "Esta persona le ha bloqueado, o usted ha bloqueado a esta persona, por lo tanto no puede revisar su perfil."
				]);
			}

		} else {
			$profile = $request->person;
			$ownProfile = true;
		}

		unset($profile->credit, $profile->tickets, $profile->cellphone);
		Social::getTags($profile);

		// pass variables to the template
		$content->profile = $profile;
		$content->ownProfile = $ownProfile;

		$pathToService = Utils::getPathToService($response->serviceName);
		if (!empty($profile->avatar)) {
			$images = ["$pathToService/images/{$profile->avatar}.png"];
		} else $images = ["$pathToService/images/hombre.png"];

		// create a new Response object and input the template and the content
		if (!$ownProfile) {
			$response->setCache(240);
		}

		//Core::log(json_encode($content), "debug");
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
		$content = [
			'profile' => $request->person,
			'cellphoneUpdateAllowed' => $this->cellphoneUpdatesThisYear($request->person) < 2
		];

		$pathToService = Utils::getPathToService($response->serviceName);
		if (!empty($request->person->avatar)) {
			$images = ["$pathToService/images/{$request->person->avatar}.png"];
		} else $images = ["$pathToService/images/hombre.png"];

		$response->setTemplate('edit.ejs', $content, $images);
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
			ORDER BY value", true, 'utf8mb4');

		// send data to the view
		$response->setCache();
		$response->setTemplate('experience.ejs', ['experience' => $experience]);
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
		$images = [];
		foreach ($this->avatars as $avatar) $images[] = "$pathToService/images/$avatar.png";

		$response->setTemplate('avatar_select.ejs', [
			'currentAvatar' => $request->person->avatar,
			'currentColor' => $request->person->avatarColor
		], $images);
	}

	/**
	 * Edit your images
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws Exception
	 */
	public function _imagenes(Request $request, Response $response)
	{
		$id = $request->input->data->id ?? $request->person->id;
		$ownProfile = $request->person->id == $id;

		$imagesList = q("SELECT id, file, `default` FROM person_images WHERE id_person = '$id' AND active=1");

		$images = [];
		foreach ($imagesList as $image) {
			$image->file = Utils::generateThumbnail($image->file);
			$images[] = $image->file;
		}

		$response->setTemplate('images.ejs', ['images' => $imagesList, 'ownProfile' => $ownProfile, "idPerson" => $id], $images);
	}

	public function _ver(Request $request, Response $response)
	{
		$id = $request->input->data->id;
		$image = $id != "last" ? q("SELECT * FROM person_images WHERE id='$id'")[0] : q("SELECT * FROM person_images WHERE id_person='{$request->person->id}' ORDER BY id DESC LIMIT 1")[0];
		$image->file = Core::getRoot() . "/shared/img/profile/{$image->file}.jpg";
		$ownProfile = $image->id_person == $request->person->id;
		$response->setTemplate('displayImage.ejs', ['image' => $image, 'ownProfile' => $ownProfile], [$image->file]);
	}

	public function _borrar(Request $request, Response $response)
	{
		$id = $request->input->data->id;
		$default = q("SELECT `default` FROM person_images WHERE id='$id'")[0]->default == "1";
		Connection::query("UPDATE person_images SET active=0 WHERE id='$id' AND id_person='{$request->person->id}'");
		if ($default) Connection::query("UPDATE person SET picture = NULL WHERE id='{$request->person->id}'");
		unset($request->input->data->id);
		$this->_imagenes($request, $response);
	}

	/**
	 * Subservice FOTO
	 *
	 * @param Request $request
	 * @param Response $response
	 *
	 * @throws \Exception
	 */
	public function _foto(Request $request, Response $response)
	{
		// do not allow empty files
		if (isset($request->input->data->picture)) {
			$picture = $request->input->data->picture;
			$updatePicture = $request->input->data->updatePicture ?? false;

			// get the image name and path
			$fileName = Utils::generateRandomHash();
			$filePath = Core::getRoot() . "/shared/img/profile/$fileName.jpg";

			// save the optimized image on the user folder
			file_put_contents($filePath, base64_decode($picture));
			Utils::optimizeImage($filePath);

			// save changes on the database
			Connection::query("INSERT INTO person_images(id_person, file) VALUES('{$request->person->id}', '$fileName')");
			if ($updatePicture) {
				q("
					UPDATE person SET picture='$fileName' WHERE id='{$request->person->id}';
					UPDATE person_images SET `default`=0 WHERE id_person='{$request->person->id}';
					UPDATE person_images SET `default`=1 WHERE file='$fileName';
					");
			}
		} else if (isset($request->input->data->id)) {
			$id = $request->input->data->id;
			$image = q("SELECT file FROM person_images WHERE id='$id' AND id_person='{$request->person->id}'")[0]->file ?? false;
			if ($image) q("
							UPDATE person SET picture='$image' WHERE id='{$request->person->id}';
							UPDATE person_images SET `default`=0 WHERE id_person='{$request->person->id}';
							UPDATE person_images SET `default`=1 WHERE id='$id';
							");

		} else return;

		if (isset($request->person->completion) && $request->person->completion > 70) {
			Challenges::complete('complete-profile', $request->person->id);
		}

		Challenges::complete("update-profile-picture", $request->person->id);
	}

	/**
	 * Show the form of where you hear about the app
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws \Exception
	 */
	public function _origen(Request $request, Response $response)
	{
		// get the person to add origin
		$content = new stdClass();
		$content->origin = $request->person->origin;
		$content->origins = $this->origins;

		// complete challenge
		Challenges::complete("where-found-apretaste", $request->person->id);

		// send data to the view
		$response->setTemplate('origin.ejs', $content);
	}

	/**
	 * Block an user
	 *
	 * @param Request $request
	 * @param Response $response
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
	 * @param Request $request
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

			if ($key === 'cellphone') {
				$updatesThisYear = $this->cellphoneUpdatesThisYear($request->person);
				if ($updatesThisYear >= 2) continue;
				else {
					if (!$request->person->cellphone) $request->person->cellphone = "NULL";
					Connection::query("INSERT INTO person_cellphone_update(person_id, previous_cellphone, new_cellphone) VALUES('{$request->person->id}', '{$request->person->cellphone}', '$value')");
				}
			}

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
			Connection::query("UPDATE person SET " . implode(",", $pieces) . " WHERE id={$request->person->id}", true, "utf8mb4");
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

	private function cellphoneUpdatesThisYear($person): int
	{
		$updatesThisYear = Connection::query("SELECT COUNT(id) AS total FROM person_cellphone_update WHERE person_id = '{$person->id}' AND YEAR(NOW())=YEAR(updated)");
		return (int)$updatesThisYear[0]->total;
	}
}
