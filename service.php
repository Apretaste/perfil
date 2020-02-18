<?php

use Apretaste\Amulets;
use Apretaste\Challenges;
use Apretaste\Chats;
use Apretaste\Level;
use Apretaste\Notifications;
use Apretaste\Person;
use Apretaste\Request;
use Apretaste\Response;
use Framework\Alert;
use Framework\Core;
use Framework\Database;
use Framework\Images;
use Framework\Utils;

class Service
{
	/**
	 * Display your profile
	 *
	 * @param Request $request
	 * @param Response $response
	 * @return Response|void
	 * @throws Exception
	 */
	public function _main(Request $request, Response $response)
	{
		// get the email or the username for the profile
		$data = $request->input->data;
		$content = new stdClass();

		if (!empty($data->username) && $data->username != $request->person->username) {
			// get the data of the person requested
			$profile = Person::find($data->username);

			// run powers for amulet DETECTIVE
			if (Amulets::isActive(Amulets::DETECTIVE, $profile->id)) {
				$msg = "Los poderes del amuleto del Druida te avisan: @{$request->person->username} está revisando tu perfil";
				Notifications::alert($profile->id, $msg, 'pageview', '{command:"PERFIL", data:{username:"@{$request->person->username}"}}');
			}

			// run powers for amulet SHADOWMODE
			if (Amulets::isActive(Amulets::SHADOWMODE, $profile->id)) {
				return $response->setTemplate('message.ejs', [
					'header' => 'Shadow-Mode',
					'icon' => 'visibility_off',
					'text' => 'La magia oscura de un amuleto rodea este perfil y te impide verlo. Por mucho que intentes romperlo, el hechizo del druida es poderoso.'
				]);
			}

			// check if the person exist. If not, message the requestor
			if (!$profile) {
				return $response->setTemplate('message.ejs', [
					'header' => 'El perfil no existe',
					'icon' => 'sentiment_very_dissatisfied',
					'text' => 'Lo sentimos, pero el perfil que usted busca no pudo ser encontrado. Puede que el nombre de usuario halla cambiado o la persona halla salido de la app.'
				]);
			}

			// prepare the profile for the person requested
			$ownProfile = $profile->id === $request->person->id;

			// check if current user blocked the user to lookup, or is blocked by
			$blocks = Chats::isBlocked($request->person->id, $profile->id);
			if ($blocks->blocked || $blocks->blockedByMe) {
				return $response->setTemplate('message.ejs', [
					'header' => 'Perfil bloqueado',
					'icon' => 'sentiment_very_dissatisfied',
					'text' => 'Esta persona le ha bloqueado, o usted ha bloqueado a esta persona, por lo tanto no puede revisar su perfil.'
				]);
			}
		} else {
			$profile = $request->person;
			$ownProfile = true;
		}

		unset($profile->credit, $profile->tickets, $profile->cellphone);
		Person::setProfileTags($profile);

		// pass variables to the template
		$content->profile = $profile;
		$content->ownProfile = $ownProfile;

		// get the gem image
		$images = SERVICE_PATH . 'perfil/images/' . 'level-' . strtolower($request->person->level) . '.png';

		// cache if seeing someone else's profile
		if (!$ownProfile) {
			$response->setCache();
		}

		// send data to the template
		$response->setTemplate('profile.ejs', $content, [$images]);
	}

	/**
	 * Edit your profile
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws Alert
	 */
	public function _editar(Request $request, Response $response)
	{
		$response->setTemplate('edit.ejs', ['profile' => $request->person]);
	}

	/**
	 * Get the list of levels
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws Alert
	 */
	public function _niveles(Request $request, Response $response)
	{
		// get gem images
		$images = [];
		foreach (['zafiro', 'topacio', 'rubi', 'opalo', 'esmeralda', 'diamante'] as $gem) {
			$images[] = SERVICE_PATH . 'perfil/images/' . 'level-' . $gem . '.png';
		}

		// send response to the view
		$response->setTemplate('levels.ejs', ['experience' => $request->person->experience], $images);
	}

	/**
	 * Get ways of gaining experience
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws Alert
	 */
	public function _experiencia(Request $request, Response $response)
	{
		// get the experience leve
		$experience = Database::query('
			SELECT description, value
			FROM person_experience_rules
			WHERE active = 1
			ORDER BY value');

		// send data to the view
		$response->setCache();
		$response->setTemplate('experience.ejs', ['experience' => $experience]);
	}

	/**
	 * Edit your avatar
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws Alert
	 */
	public function _avatar(Request $request, Response $response)
	{
		$response->setTemplate('avatar_select.ejs', [
			'currentAvatar' => $request->person->avatar,
			'currentColor' => $request->person->avatarColor
		]);
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 * @throws Alert
	 */
	public function _ver(Request $request, Response $response)
	{
		$id = $request->input->data->id;
		$image = $id !== 'last' ? Database::query("SELECT * FROM person_images WHERE id='$id'")[0] : Database::query("SELECT * FROM person_images WHERE id_person='{$request->person->id}' ORDER BY id DESC LIMIT 1")[0];
		$image->file = IMG_PATH . "/profile/{$image->file}.jpg";
		$ownProfile = $image->id_person == $request->person->id;
		$response->setTemplate('displayImage.ejs', ['image' => $image, 'ownProfile' => $ownProfile], [$image->file]);
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 * @throws Alert
	 * @throws Exception
	 */
	public function _borrar(Request $request, Response $response)
	{
		$id = $request->input->data->id;
		$default = Database::query("SELECT `default` FROM person_images WHERE id='$id'")[0]->default == '1';
		Database::query("UPDATE person_images SET active=0 WHERE id='$id' AND id_person='{$request->person->id}'");
		if ($default) {
			Database::query("UPDATE person SET picture = NULL WHERE id='{$request->person->id}'");
		}
		unset($request->input->data->id);
		$this->_imagenes($request, $response);
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
		// get the ID of the person to check
		$id = $request->input->data->id ?? $request->person->id;

		// get the list of images for the person
		$imagesList = Database::query("SELECT id, file, `default` FROM person_images WHERE id_person=$id AND active=1");

		// thumbnail the images
		$images = [];
		foreach ($imagesList as $image) {
			$image->file = $image->file . '.jpg'; // update img for the view
			$imgPath = IMG_PATH . 'profile/' . $image->file;
			$images[] = Images::thumbnail($imgPath);
		}

		// create the content
		$content = [
			'images' => $imagesList, 
			'ownProfile' => $request->person->id == $id, 
			'idPerson' => $id];

		// send data to the view
		$response->setTemplate('images.ejs', $content, $images);
	}

	/**
	 * Subservice FOTO
	 *
	 * @param Request $request
	 * @param Response $response
	 *
	 * @throws Exception
	 */
	public function _foto(Request $request, Response $response)
	{
		// do not allow empty files
		if (isset($request->input->data->picture)) {
			$picture = $request->input->data->picture;
			$updatePicture = $request->input->data->updatePicture ?? false;

			// get the image name and path
			$fileName = Utils::randomHash();
			$filePath = IMG_PATH . "/profile/$fileName.jpg";

			// save and optimize the image on the user folder
			file_put_contents($filePath, base64_decode($picture));
			Images::optimize($filePath);

			// save changes on the database
			Database::query("INSERT INTO person_images(id_person, file) VALUES('{$request->person->id}', '$fileName')");
			if ($updatePicture) {
				Database::query("
					UPDATE person SET picture='$fileName' WHERE id='{$request->person->id}';
					UPDATE person_images SET `default`=0 WHERE id_person='{$request->person->id}';
					UPDATE person_images SET `default`=1 WHERE file='$fileName';
					");
			}
		} elseif (isset($request->input->data->id)) {
			$id = $request->input->data->id;
			$image = Database::query("SELECT file FROM person_images WHERE id='$id' AND id_person='{$request->person->id}'")[0]->file ?? false;
			if ($image) {
				Database::query("
							UPDATE person SET picture='$image' WHERE id='{$request->person->id}';
							UPDATE person_images SET `default`=0 WHERE id_person='{$request->person->id}';
							UPDATE person_images SET `default`=1 WHERE id='$id';
							");
			}
		} else {
			return;
		}

		if (isset($request->person->completion) && $request->person->completion > 70) {
			Challenges::complete('complete-profile', $request->person->id);
		}

		Challenges::complete('update-profile-picture', $request->person->id);
	}

	/**
	 * Show the form of where you hear about the app
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws Exception
	 */
	public function _origen(Request $request, Response $response)
	{
		// get the person to add origin
		$content = new stdClass();
		$content->origin = $request->person->origin;
		$content->origins = Core::$origins;

		// complete challenge
		Challenges::complete('where-found-apretaste', $request->person->id);

		// send data to the view
		$response->setTemplate('origin.ejs', $content);
	}

	/**
	 * Block an user
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws Exception
	 * @author ricardo@apretaste.com
	 */
	public function _bloquear(Request $request, Response $response)
	{
		$person = Person::find($request->input->data->username);
		$fromId = $request->person->id;

		if ($person) {
			$r = Database::query("SELECT * FROM relations WHERE user1='$fromId' AND user2='$person->id'");
			if (isset($r[0])) {
				Database::query("UPDATE relations SET confirmed=1 WHERE user1='$fromId' AND user2='$person->id' AND `type`='blocked'");
			} else {
				Database::query("INSERT INTO `relations`(user1,user2,`type`,confirmed) VALUES ('$fromId','$person->id','blocked',1)");
			}
		}

		$this->_main($request, $response);
	}

	/**
	 * unlock an user
	 *
	 * @param Request
	 * @param Response $response
	 *
	 * @throws Exception
	 * @author ricardo@apretaste.com
	 */
	public function _desbloquear(Request $request, Response $response)
	{
		$person = Person::find($request->input->data->username);
		$fromId = $request->person->id;
		if ($person) {
			Database::query("
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
	 * @throws Exception
	 * @author salvipascual
	 * @version 1.0
	 */
	public function _update(Request $request, Response $response)
	{
		// possible fields to update in the database
		$fields = ['username', 'first_name', 'middle_name', 'last_name', 'mother_name', 'about_me', 'avatar', 'avatarColor', 'year_of_birth', 'month_of_birth', 'day_of_birth', 'gender', 'cellphone', 'eyes', 'skin', 'body_type', 'hair', 'province', 'city', 'highest_school_level', 'occupation', 'marital_status', 'interests', 'about_me', 'mail_list', 'picture', 'sexual_orientation', 'religion', 'origin', 'show_images', 'country', 'usstate'];

		// clean, shorten and lowercase the username, if passed
		if (!empty($request->input->data->username)) {
			$username = preg_replace('/[^a-zA-Z0-9]+/', '', $request->input->data->username);
			$request->input->data->username = strtolower(substr($username, 0, 15));
			if (Person::find($username)) {
				Notifications::alert($request->person->id, "Lo sentimos, el username @$username ya esta siendo usado");
				unset($request->input->data->username);
			}
		}

		// get the JSON with the bulk
		$pieces = [];
		foreach ($request->input->data as $key => $value) {
			// format first_name OR last_name in capital the first letter
			if ($key === 'first_name' || $key === 'last_name') {
				$value = ucfirst(strtolower($value));
			}

			// format interests as a CVS to be saved
			if ($key === 'interests') {
				$interests = [];
				foreach ($value as $piece) {
					$interests[] = $piece->tag;
				}
				$value = implode(',', $interests);
			}

			// escape dangerous chars in the value passed
			$value = Database::escape($value);

			// prepare the database query
			if (in_array($key, $fields, true)) {
				if ($value === null || $value === '') {
					$pieces[] = "$key = null";
				} else {
					$pieces[] = "$key = '$value'";
				}

				if ($key === 'avatar') {
					Challenges::complete('update-profile-picture', $request->person->id);
				}
			}
		}

		// save changes on the database
		if (!empty($pieces)) {
			Database::query('UPDATE person SET ' . implode(',', $pieces) . " WHERE id={$request->person->id}");
		}

		// add the experience if profile is completed
		if ($request->person->completion > 80) {
			Challenges::complete('complete-profile', $request->person->id);
			Level::setExperience('FINISH_PROFILE_FIRST', $request->person->id);
		}
	}
}
