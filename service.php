<?php

use Apretaste\Core;
use Apretaste\Alert;
use Apretaste\Utils;
use Apretaste\Money;
use Apretaste\Images;
use Apretaste\Bucket;
use Apretaste\Person;
use Apretaste\Amulets;
use Apretaste\Request;
use Apretaste\Response;
use Apretaste\Database;
use Apretaste\Tutorial;
use Apretaste\Challenges;
use Apretaste\Notifications;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;

class Service
{
	/**
	 * Display your profile
	 *
	 * @param Request $request
	 * @param Response $response
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

				// check if current user blocked the user to lookup, or is blocked by
				$relation = $request->person->getBlockedRelation($profile->id);
				if ($relation) {
					$youBlockTheUser = ((int)$relation->user1 === $request->person->id) || ((int)$relation->user2 === $request->person->id && (int)$relation->bi === 1);
					$response->setComponent('Message', [
						'header' => 'Perfil bloqueado',
						'icon' => 'fa-ban',
						'text' => 'No puede revisar su perfil',
						'blockOption' => !$youBlockTheUser,
						'profile' => $profile
					]);

					return;
				}

				// run powers for amulet SHADOWMODE
				if (Amulets::isActive(Amulets::SHADOWMODE, $profile->id) && !$request->person->isFriendOf($profile->id)/* @note && !$ownProfile */) {
					$response->setComponent('Message', [
						'header' => 'Shadow-Mode',
						'icon' => 'fa fa-eye-slash',
						'text' => 'La magia oscura de un amuleto rodea este perfil y te impide verlo. Por mucho que intentes romperlo, el hechizo del druida es poderoso.',
						'blockOption' => true,
						'profile' => $profile
					]);

					return;
				}

				// run powers for amulet DETECTIVE
				if (Amulets::isActive(Amulets::DETECTIVE, $profile->id)) {
					$msg = "@{$request->person->username} está revisando tu perfil";
					Notifications::alert($profile->id, $msg, 'pageview', "{command:'PERFIL', data:{username:'@{$request->person->username}'}}");
				}
			}
		} else {
			$profile = Person::find($request->person->username);
			$ownProfile = true;
		}

		// check if the person exist. If not, message the requestor
		if (!$profile) {
			$response->setComponent('Message', [
				'header' => 'El perfil no existe',
				'icon' => 'fa fa-sad-cry',
				'text' => 'Lo sentimos, pero el perfil que usted busca no pudo ser encontrado. Puede que el nombre de usuario haya cambiado o la persona haya salido de la app.'
			]);
			return;
		}

		// set the tags
		Person::setProfileTags($profile);

		// check if the person has no social links
		$emptySocialLinks = empty($profile->facebook) && empty($profile->twitter) && empty($profile->instagram) && empty($profile->telegram) && empty($profile->whatsapp) && empty($profile->website);

		$images = [];
		if ($profile->picture) {
			$images[] = Bucket::get('perfil', $profile->picture);
		}

		foreach ($profile->gallery as $img) {
			$images[] = Bucket::get('perfil', $img->file);
		}

		// pass variables to the template
		$content = [
			'profile' => self::profileMin($profile),
			'ownProfile' => $ownProfile,
			'type' => $type ?? 'none',
			'emptySocialLinks' => $emptySocialLinks,
			'myCredit' => $request->person->credit
		];

		// cache if seeing someone else's profile
		if (!$ownProfile) {
			$response->setCache();
		}

		// send data to the template
		$response->setComponent('Main', $content, $images);
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
		if ($request->person->isInfluencer) {
			$response->setComponent('Message', [
				'header' => 'Lo sentimos',
				'icon' => 'fa fa-sad-cry',
				'text' => 'Los creadores de contenido no pueden editar su perfil directamente, contactanos para mas información.'
			]);

			return;
		}

		// create the content array
		$content = [
			'profile' => self::profileMin($request->person)
		];

		// crate send information to the view
		$response->setComponent('Edit', $content);
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
		$response->setComponent('Levels', ['experience' => $request->person->experience], $images);
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
		// complete tutorial
		Tutorial::complete($request->person->id, 'check_ranking');

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
		$response->setComponent('Experience', ['experience' => $experience]);
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
		$influencer = $request->input->data->influencer;
		$amount = $request->input->data->amount;

		if ($request->person->credit < $amount || $amount < 0.1) {
			$response->setComponent('Message', [
				'header' => 'Crédito insuficiente',
				'icon' => 'fa fa-sad-cry',
				'text' => "No tienes suficiente crédito, tu crédito actual es §{$request->person->credit}."
			]);
			return;
		}

		$isCreator = Database::queryFirst("SELECT username, is_influencer FROM person WHERE id='$influencer'");
		if ($isCreator && $isCreator->is_influencer) {
			try {

				// transfer credit
				Money::transfer($request->person->id, $influencer, $amount, 'DONATION');

				// send notification
				Notifications::alert($influencer, "@{$request->person->username} te ha donado §$amount", 'attach_money',
					(object)[
						'command' => 'PERFIL',
						'data' => [
							'id' => $request->person->id
						]
					]
				);

				$response->setComponent('Message', [
					'header' => 'Su donación se ha realizado',
					'icon' => 'fa fa-dollar-sign',
					'text' => "Gracias por donar §$amount de crédito a @{$isCreator->username}. Estos fondos serán usados para llegar a más personas y hacer un trabajo de más calidad."
				]);
			} catch (Alert $alert) {
				$response->setComponent('Message', [
					'header' => 'Error al transferir',
					'icon' => 'fa fa-sad-cry',
					'text' => $alert->message
				]);
			}

		} else {
			$response->setComponent('Message', [
				'header' => 'Usuario invalido',
				'icon' => 'fa fa-sad-cry',
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
		$pictureName = $request->input->data->pictureName ?? false;
		$updatePicture = $request->input->data->updatePicture ?? false;

		// do not allow empty files
		if ($pictureName) {
			// get the image name and path
			$fileName = Utils::randomHash() . '.jpg';
			$filePath = $request->input->files[$pictureName];

			Bucket::save("perfil", $filePath, $fileName);

			if ($updatePicture) {
				// new profile picture
				Database::query("
					UPDATE person SET picture='$fileName' WHERE id='{$request->person->id}';
					UPDATE person_images SET `default`=0 WHERE id_person='{$request->person->id}';
					UPDATE person_images SET `default`=1 WHERE file='$fileName';");

				if (isset($request->person->completion) && $request->person->completion > 70) {
					Challenges::complete('complete-profile', $request->person->id);
				}

				Challenges::complete('update-profile-picture', $request->person->id);
			} else {
				// new picture in the gallery
				$id = Database::query("INSERT INTO person_images(id_person, file) VALUES('{$request->person->id}', '$fileName')");
				$image = Bucket::get('perfil', $fileName);
				// notify to friends
				Notifications::alertMyFriends($request->person->id,
					"@{$request->person->username} ha publicado una nueva foto en su galería",
					'info_outline', "{command: \"PERFIL IMAGENES\", data:{id: {$request->person->id}}}");

				$response->setContent([
					'id' => $id,
					'file' => basename($image)
				], [$image]);
			}
		}
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
		$request->person->blockPerson($person->id);
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
		$request->person->unblockPerson($person->id);
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
		if (isset($request->input->data->username)) {
			$u = $request->input->data->username;
			if (substr(strtolower($u), -3) === 'bot') {
				return;
			}
		}

		// update the profile
		Person::update($request->person->id, $request->input->data);

		// complete tutorial
		Tutorial::complete($request->person->id, 'fill_profile');
	}

	/**
	 * Function to inactive the user when logout
	 * @param Request $request
	 * @param Response $response
	 * @throws Alert
	 */
	public function _salir(Request $request, Response $response)
	{
		Database::query("UPDATE person SET status='SLEEP', last_logout = CURRENT_TIMESTAMP WHERE id={$request->person->id}");
		Database::query("DELETE FROM tokens WHERE person_id={$request->person->id} AND token_type='apretaste:firebase'");
	}

	/**
	 * @param Person $person
	 * @return object
	 * @throws Alert
	 */
	private static function profileMin(Person $person): object
	{
		if ($person->isInfluencer) {
			return (object)[
				'id' => $person->id,
				'username' => $person->username,
				'aboutMe' => $person->aboutMe,
				'gender' => $person->gender,
				'interests' => $person->interests,
				'facebook' => $person->facebook ?? false,
				'twitter' => $person->twitter ?? false,
				'instagram' => $person->instagram ?? false,
				'telegram' => $person->telegram ?? false,
				'whatsapp' => $person->whatsapp ?? false,
				'website' => $person->website ?? false,
				'tags' => $person->tags,
				'isInfluencer' => true,
				'influencerData' => $person->getInfluencerData()
			];
		}

		return (object)[
			'id' => $person->id,
			'username' => $person->username,
			'aboutMe' => $person->aboutMe,
			'firstName' => $person->firstName,
			'lastName' => $person->lastName,
			'fullName' => $person->fullName,
			'gender' => $person->gender,
			'picture' => $person->picture,
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
			'friendList' => $person->getFriendsCount(),
			'experience' => $person->experience,
			'ranking' => $person->weekRank,
			'tags' => $person->tags,
			'facebook' => $person->facebook ?? false,
			'twitter' => $person->twitter ?? false,
			'instagram' => $person->instagram ?? false,
			'telegram' => $person->telegram ?? false,
			'whatsapp' => $person->whatsapp ?? false,
			'website' => $person->website ?? false,
			'isInfluencer' => false,
			'gallery' => $person->gallery
		];
	}
}
