<?php

namespace modules\amocrm\controllers;


use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Collections\TagsCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\NumericCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\NumericCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\NumericCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\TagModel;
use AmoCRM\Models\Unsorted\FormsMetadata;
use core\Controller;
use FontLib\Table\Type\nameRecord;
use League\OAuth2\Client\Token\AccessTokenInterface;
use modules\amocrm\models\AmoCrmFields;
use modules\user_diet_extension\models\User_diet_extension;


class Amo extends Controller
{
	const LEADS_FIELD   = 1;
	const CONTACT_FIELD = 2;

	static function instance()
	{
		return new Amo();
	}

	public function actionToken()
	{
		include_once __DIR__ . '/../helpers/bootstrap.php';
		/**
		 * Создаем провайдера
		 */

		if (isset($_GET['referer'])) {
			$apiClient->setAccountBaseDomain($_GET['referer']);
		}


		if (!isset($_GET['code'])) {
			$state = bin2hex(random_bytes(16));
			$_SESSION['oauth2state'] = $state;
			if (isset($_GET['button'])) {
				echo $apiClient->getOAuthClient()->getOAuthButton(
					[
						'title'          => 'Установить интеграцию',
						'compact'        => true,
						'class_name'     => 'className',
						'color'          => 'default',
						'error_callback' => 'handleOauthError',
						'state'          => $state,
					]
				);
				die;
			} else {
				$authorizationUrl = $apiClient->getOAuthClient()->getAuthorizeUrl([
					'state' => $state,
					'mode'  => 'post_message',
				]);
				header('Location: ' . $authorizationUrl);
				die;
			}
		} elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
			unset($_SESSION['oauth2state']);
			exit('Invalid state');
		}

		/**
		 * Ловим обратный код
		 */
		try {
			$accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($_GET['code']);

			if (!$accessToken->hasExpired()) {
				saveToken([
					'accessToken'  => $accessToken->getToken(),
					'refreshToken' => $accessToken->getRefreshToken(),
					'expires'      => $accessToken->getExpires(),
					'baseDomain'   => $apiClient->getAccountBaseDomain(),
				]);
			}
		} catch (AmoCRMApiException $e) {
			die((string)$e);
		}

		$ownerDetails = $apiClient->getOAuthClient()->getResourceOwner($accessToken);

		printf('Hello, %s!, первичный токен получен', $ownerDetails->getName());
	}

	public function authCrm()
	{
		/**
		 * Создаем провайдера
		 */

		include_once __DIR__ . '/../helpers/bootstrap.php';

		$accessToken = getToken();

		$apiClient->setAccessToken($accessToken)
				  ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
				  ->onAccessTokenRefresh(
					  function (AccessTokenInterface $accessToken, string $baseDomain) {
						  saveToken(
							  [
								  'accessToken'  => $accessToken->getToken(),
								  'refreshToken' => $accessToken->getRefreshToken(),
								  'expires'      => $accessToken->getExpires(),
								  'baseDomain'   => $baseDomain,
							  ]
						  );
					  }
				  );

		return $apiClient;

	}

	public function Leads(array $data)
	{
		$apiClient = $this->authCrm();

		$fieldsArr = AmoCrmFields::instance()->getAll(['category' => 1]);

		$data_leads = [];
		foreach ($fieldsArr as $Row) {
			if (!empty($Row['name_in_form']) && array_key_exists($Row['name_in_form'], $data)) {
				$arr = [
					'id'    => $Row['id_field'],
					'type'  => $Row['type'],
					'code'  => $Row['code'],
					'value' => $data[$Row['name_in_form']],
				];
				array_push($data_leads, $arr);
			}
		}


		$lead = new LeadModel();
		$leadCustomFieldsValues = new CustomFieldsValuesCollection();

		foreach ($data_leads as $Row) {

			$CustomFieldValueModel = new TextCustomFieldValuesModel();

			if ($Row['type'] == 'numeric') {
				$CustomFieldValueModel = new NumericCustomFieldValuesModel();
			}

			$CustomFieldValueModel->setFieldId($Row['id']);

			if ($Row['type'] == 'text') {
				$CustomFieldValueModel->setValues(
					(new TextCustomFieldValueCollection())
						->add((new TextCustomFieldValueModel())->setValue($Row['value']))
				);
			}
			if ($Row['type'] == 'numeric') {
				$CustomFieldValueModel->setValues(
					(new NumericCustomFieldValueCollection())
						->add((new NumericCustomFieldValueModel())->setValue($Row['value']))
				);
			}

			$leadCustomFieldsValues->add($CustomFieldValueModel);
		}


		$lead->setName((string)$data['name'])
			 ->setPrice((int)$data['price'])
			 ->setCustomFieldsValues($leadCustomFieldsValues)
			 ->setRequestId($data['external_id']);

		if (isset($data['tag'])) {
			$lead->setTags(
				(new TagsCollection())
					->add(
						(new TagModel())
							->setName((string)$data['tag'])
					)
			);
		}

		if ($data['is_new']) {
			$lead->setMetadata(
				(new FormsMetadata())
					->setFormId('my_best_form')
					->setFormName('Оформление тарифа')
					->setFormPage($data['url_form'])
					->setFormSentAt(mktime(date('h'), date('i'), date('s'), date('m'), date('d'), date('Y')))
					->setReferer('https://google.com/search')
					->setIp('192.168.0.1')
			);
		}

		$addLead = $apiClient->leads()->addOne($lead);

		//Создание контакта
		if (empty($data['crm_id'])) {
			$contact = new ContactModel();
		} else {
			$contact = $apiClient->contacts()->getOne($data['crm_id']);
		}
		if (isset($data['contact']['name'])) {
			$contact->setName($data['contact']['name']);
		}
		if (isset($data['contact']['first_name'])) {
			$contact->setFirstName($data['contact']['first_name']);
		}
		if (isset($data['contact']['last_name'])) {
			$contact->setLastName($data['contact']['last_name']);
		}
		$contact->setCustomFieldsValues(
			(new CustomFieldsValuesCollection())
				->add(
					(new MultitextCustomFieldValuesModel())
						->setFieldCode('PHONE')
						->setValues(
							(new MultitextCustomFieldValueCollection())
								->add(
									(new MultitextCustomFieldValueModel())
										->setEnum('MOB')
										->setValue($data['contact']['phone'])
								)
						)
				)
				->add(
					(new MultitextCustomFieldValuesModel())
						->setFieldCode('EMAIL')
						->setValues(
							(new MultitextCustomFieldValueCollection())
								->add(
									(new MultitextCustomFieldValueModel())
										->setEnum('PRIV')
										->setValue($data['contact']['email'])
								)
						)
				)
		);
		if (empty($data['crm_id'])) {
			$contactsCollection = new ContactsCollection();
			$contactsCollection->add($contact);
			$contactModel = $apiClient->contacts()->add($contactsCollection);
			$contacResult = $contactModel->toArray();
			$contact_id = $contacResult[0]['id'];
		} else {
			$contactModel = $apiClient->contacts()->updateOne($contact);
			$contacResult = $contactModel->toArray();
			$contact_id = $contacResult['id'];
		}


		//Привязка Контакта к Сделке
		try {
			$lead = $apiClient->contacts()->getOne($contact_id);
		} catch (AmoCRMApiException $e) {
			printError($e);
			die;
		}

		$links = new LinksCollection();
		$links->add($lead);
		try {
			$apiClient->leads()->link($addLead, $links);

			return $contact_id;

		} catch (AmoCRMApiException $e) {
			printError($e);
			die;
		}

	}
}
