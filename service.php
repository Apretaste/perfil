<?php

use Apretaste\Chats;
use Apretaste\Level;
use Apretaste\Money;
use Apretaste\Person;
use Apretaste\Amulets;
use Apretaste\Request;
use Apretaste\Response;
use Apretaste\Challenges;
use Apretaste\Notifications;
use Framework\Core;
use Framework\Utils;
use Framework\Alert;
use Framework\Images;
use Framework\Database;
use Framework\GoogleAnalytics;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;

class Service
{
	/**
	 * Display your profile
	 *
	 * @param Request $request
	 * @param Response $response
	 * @return Response|void
	 * @throws Alert
	 */
	public function _main(Request $request, Response $response)
	{
		// get the id or the username for the profile
		$data = $request->input->data;
		$needle = $data->username ?? $data->id ?? false;
		$ownProfile = $needle == $request->person->id || str_replace('@', '', $needle) == $request->person->username;

		if ($needle && !$ownProfile) {
			// get the data of the person requested
			$profile = Person::find($needle);

			if ($profile != null) {
				$type = $profile->isFriendOf($request->person->id) ? 'friends' : 'none';
				if ($type == 'none') {
					$waiting = $profile->getWaitingRelation($request->person->id);
					if ($waiting) {
						if ($waiting->user1 == $profile->id) $type = 'waitingForMe';
						else $type = 'waiting';
					}
				}

				// run powers for amulet DETECTIVE
				if (Amulets::isActive(Amulets::DETECTIVE, $profile->id)) {
					$msg = "Los poderes del amuleto del Druida te avisan: @{$request->person->username} está revisando tu perfil";
					Notifications::alert($profile->id, $msg, 'pageview', "{command:'PERFIL', data:{username:'@{$request->person->username}'}}");
				}

				// run powers for amulet SHADOWMODE
				if (Amulets::isActive(Amulets::SHADOWMODE, $profile->id)) {
					return $response->setTemplate('message.ejs', [
						'header' => 'Shadow-Mode',
						'icon' => 'visibility_off',
						'text' => 'La magia oscura de un amuleto rodea este perfil y te impide verlo. Por mucho que intentes romperlo, el hechizo del druida es poderoso.'
					]);
				}

				// check if current user blocked the user to lookup, or is blocked by
				$blocks = Chats::isBlocked($request->person->id, $profile->id);
				if ($blocks->blocked || $blocks->blockedByMe) {
					return $response->setTemplate('message.ejs', [
						'header' => 'Perfil bloqueado',
						'icon' => 'sentiment_very_dissatisfied',
						'text' => 'Esta persona le ha bloqueado, o usted ha bloqueado a esta persona, por lo tanto no puede revisar su perfil.'
					]);
				}
			}
		} else {
			$profile = Person::find($request->person->username);
			$ownProfile = true;
		}

		// check if the person exist. If not, message the requestor
		if (!$profile) {
			$response->setLayout('perfil.ejs');
			return $response->setTemplate('message.ejs', [
				'header' => 'El perfil no existe',
				'icon' => 'sentiment_very_dissatisfied',
				'text' => 'Lo sentimos, pero el perfil que usted busca no pudo ser encontrado. Puede que el nombre de usuario haya cambiado o la persona haya salido de la app.'
			]);
		}

		Person::setProfileTags($profile);

		// pass variables to the template
		$content = [
			'profile' => self::profileMin($profile),
			'ownProfile' => $ownProfile,
			'type' => $type ?? 'none',
			'title' => 'Perfil'
		];

		// cache if seeing someone else's profile
		if (!$ownProfile) {
			$response->setCache();
		}

		$template = $profile->isContentCreator ? 'main-cdc.ejs' : 'main.ejs';

		// send data to the template
		$response->setLayout('perfil.ejs');
		$response->setTemplate($template, $content);
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
		if ($request->person->isContentCreator) {
			$response->setTemplate('message.ejs', [
				'header' => 'Lo sentimos',
				'icon' => 'sentiment_very_dissatisfied',
				'text' => 'Los creadores de contenido no pueden editar su perfil directamente, contactanos para mas información.'
			]);

			return;
		}

		// create the content array
		$content = [
			'profile' => self::profileMin($request->person)
		];

		// crate send information to the view
		$response->setTemplate('edit.ejs', $content);
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
			SELECT description, value, concept
			FROM person_experience_rules
			WHERE active = 1
			ORDER BY value');

		// create array of concepts and icons
		$concepts = ['GAMING' => 'Juegos', 'EDUCATION' => 'Educación', 'POPULARITY' => 'Popularidad', 'CONNECTIVITY' => 'Conectados', 'SHOPPING' => 'Compras', 'PROFESSIONAL' => 'Profesional'];
		$conceptIcons = ['GAMING' => 'gamepad', 'EDUCATION' => 'graduation-cap', 'POPULARITY' => 'fire', 'CONNECTIVITY' => 'plug', 'SHOPPING' => 'shopping-cart', 'PROFESSIONAL' => 'account_balance'];

		// get the right concept and icon
		foreach ($experience as $item) {
			$item->conceptCaption = $concepts[$item->concept];
			$item->conceptIcon = $conceptIcons[$item->concept];
		}

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
	 * Display an image in the gallery
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws Alert
	 */
	public function _ver(Request $request, Response $response)
	{
		$id = $request->input->data->id;

		// get the image to display
		$image = ($id !== 'last') ?
			Database::queryFirst("SELECT * FROM person_images WHERE id='$id'") :
			Database::queryFirst("SELECT * FROM person_images WHERE id_person='{$request->person->id}' ORDER BY id DESC LIMIT 1");

		if (empty($image)) return $this->_imagenes($request, $response);

		// get the full path to the image
		$file = SHARED_PUBLIC_PATH . "profile/{$image->file}.jpg";

		// create content for the view
		$content = [
			"id" => $image->id,
			"isDefault" => $image->default,
			"file" => $image->file,
			"idPerson" => $image->id_person,
			'ownProfile' => $image->id_person == $request->person->id];

		// send data to the view
		$response->setTemplate('displayImage.ejs', $content, [$file]);
	}

	/**
	 * Delete an image from the gallery
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws Alert
	 * @throws Exception
	 */
	public function _borrar(Request $request, Response $response)
	{
		$id = $request->input->data->id;

		// delete the image
		Database::query("UPDATE person_images SET active=0 WHERE id='$id' AND id_person='{$request->person->id}'");

		// if it is the default image, delete in the person table
		$default = Database::query("SELECT `default` FROM person_images WHERE id='$id'")[0]->default == '1';
		if ($default) {
			Database::query("UPDATE person SET picture = NULL WHERE id='{$request->person->id}'");
		}

		// delete the file from the HD
		unset($request->input->data->id);

		// return the response
		$this->_imagenes($request, $response);
	}

	/**
	 * Show the image gallery
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws Exception
	 */
	public function _imagenes(Request $request, Response $response)
	{
		// get the ID of the person to check
		$id = $request->input->data->id ?? $request->person->id;
		$ownProfile = $request->person->id == $id;

		// get the list of images for the person
		$imagesList = Database::query("SELECT id, file, `default` FROM person_images WHERE id_person='$id' AND active=1");

		// thumbnail the images
		$images = [];
		foreach ($imagesList as $image) {
			$image->file = $image->file . '.jpg'; // update img for the view
			$imgPath = SHARED_PUBLIC_PATH . 'profile/' . $image->file;
			$images[] = Images::thumbnail($imgPath);
		}

		// create the content
		$content = [
			'images' => $imagesList,
			'ownProfile' => $ownProfile,
			'idPerson' => $id, 'title' => 'Imágenes'
		];

		// send data to the view
		$response->setLayout('perfil.ejs');
		$response->setTemplate('images.ejs', $content, $images);
	}


	/**
	 * Donate some credits to a content creator
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws Exception
	 */
	public function _donar(Request $request, Response $response)
	{
		$creator = $request->input->data->creator;
		$amount = $request->input->data->amount;

		if ($request->person->credit < $amount) {
			$response->setTemplate('message.ejs', [
				'header' => 'Crédito insuficiente',
				'icon' => 'sentiment_very_dissatisfied',
				'text' => "No tienes suficiente crédito, tu crédito actual es §{$request->person->credit}."
			]);
			return;
		}

		$isCreator = Database::queryFirst("SELECT username, is_content_creator FROM person WHERE id='$creator'");
		if ($isCreator && $isCreator->is_content_creator) {
			try {
				Money::transfer($request->person->id, $creator, $amount, 'DONATION');
				Notifications::alert(
					$creator, "@{$request->person->username} te ha donado §$amount",
					'attach_money', '{"command":"CREDITO"}'
				);


				$response->setTemplate('message.ejs', [
					'header' => 'Transferencia confirmada',
					'icon' => 'attach_money',
					'text' => "Has donado §$amount a @{$isCreator->username}"
				]);
			} catch (Alert $alert) {
				$response->setTemplate('message.ejs', [
					'header' => 'Error al transferir',
					'icon' => 'sentiment_very_dissatisfied',
					'text' => $alert->message
				]);
			}

		} else {
			$response->setTemplate('message.ejs', [
				'header' => 'Usuario invalido',
				'icon' => 'sentiment_very_dissatisfied',
				'text' => "El usuario al que intentas donar créditos no es un creador de contenido."
			]);
		}
	}

	/**
	 * Uploads a new image to the gallery
	 *
	 * @param Request $request
	 * @param Response $response
	 * @throws Exception
	 */
	public function _foto(Request $request, Response $response)
	{
		$picture = $request->input->data->picture ?? false;
		$pictureName = $request->input->data->pictureName ?? false;
		$updatePicture = $request->input->data->updatePicture ?? false;

		// do not allow empty files
		if ($picture || $pictureName) {
			// get the image name and path
			$fileName = Utils::randomHash();
			$filePath = SHARED_PUBLIC_PATH . "/profile/$fileName.jpg";

			if ($picture) {
				// save and optimize the image on the user folder
				Images::saveBase64Image($picture, $filePath);
			} elseif ($pictureName) {
				$tempPicturePath = $request->input->files[$pictureName];
				rename($tempPicturePath, $filePath);
			}

			// save changes on the database
			Database::query("INSERT INTO person_images(id_person, file) VALUES('{$request->person->id}', '$fileName')");
			if ($updatePicture) {
				Database::query("
					UPDATE person SET picture='$fileName' WHERE id='{$request->person->id}';
					UPDATE person_images SET `default`=0 WHERE id_person='{$request->person->id}';
					UPDATE person_images SET `default`=1 WHERE file='$fileName';");
			}
		} elseif (isset($request->input->data->id)) {
			$id = $request->input->data->id;
			$image = Database::query("SELECT file FROM person_images WHERE id='$id' AND id_person='{$request->person->id}'")[0]->file ?? false;
			if ($image) {
				Database::query("
					UPDATE person SET picture='$image' WHERE id='{$request->person->id}';
					UPDATE person_images SET `default`=0 WHERE id_person='{$request->person->id}';
					UPDATE person_images SET `default`=1 WHERE id='$id';");
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
	 * @throws Alert
	 * @throws FirebaseException
	 * @throws MessagingException
	 * @author salvipascual
	 */
	public function _update(Request $request, Response $response)
	{
		if ($request->person->isContentCreator) return;

		Person::update($request->person->id, $request->input->data);
	}

	/**
	 * Function to inactive the user when logout
	 * @param Request $request
	 * @param Response $response
	 * @throws Alert
	 */

	public function _salir(Request $request, Response $response)
	{
		Database::query("UPDATE person SET status='SLEEP' WHERE id={$request->person->id}");
		Database::query("DELETE FROM tokens WHERE person_id={$request->person->id} AND token_type='apretaste:firebase'");
	}

	/**
	 * @param Person $person
	 * @return object
	 * @throws Alert
	 */

	private static function profileMin(Person $person): object
	{
		if ($person->isContentCreator) {
			return (object)[
				'id' => $person->id,
				'username' => $person->username,
				'aboutMe' => $person->aboutMe,
				'gender' => $person->gender,
				'interests' => $person->interests,
			];
		}

		return (object)[
			'id' => $person->id,
			'avatar' => $person->avatar,
			'avatarColor' => $person->avatarColor,
			'username' => $person->username,
			'aboutMe' => $person->personToText(),
			'firstName' => $person->firstName,
			'lastName' => $person->lastName,
			'fullName' => $person->fullName,
			'gender' => $person->gender,
			'sexualOrientation' => $person->sexualOrientation,
			'dayOfBirth' => $person->dayOfBirth,
			'monthOfBirth' => $person->monthOfBirth,
			'yearOfBirth' => $person->yearOfBirth,
			'body' => $person->body,
			'eyes' => $person->eyes,
			'hair' => $person->hair,
			'skin' => $person->skin,
			'maritalStatus' => $person->maritalStatus,
			'education' => $person->education,
			'occupation' => $person->occupation,
			'country' => $person->country,
			'province' => $person->province,
			'city' => $person->city,
			'religion' => $person->religion,
			'interests' => $person->interests,
			'friendList' => $person->getFriends(),
			'experience' => $person->experience,
			'ranking' => $person->weekRank,
			'profile_tags' => $person->profile_tags ?? false,
			'profession_tags' => $person->profession_tags ?? false,
			'location_tags' => $person->location_tags ?? false
		];
	}
}
