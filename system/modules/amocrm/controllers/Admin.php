<?php


namespace modules\amocrm\controllers;


use AmoCRM\Collections\CustomFields\CustomFieldsCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\AccountModel;
use AmoCRM\Models\CustomFields\CustomFieldModel;
use AmoCRM\Models\CustomFields\NumericCustomFieldModel;
use AmoCRM\Models\CustomFields\TextCustomFieldModel;
use core\Controller;
use modules\amocrm\helpers\FiealdsHelper;
use modules\amocrm\models\AmoCrmFields;
use core\Html;
use modules\amocrm\models\AmoCRMFieldsItems;
use modules\amocrm\models\AmoCrmSettings;
use modules\user_diet_extension\models\User_diet_extension;
use function mysql_xdevapi\expression;

class Admin extends Controller
{
	/* @var $html Html */
	private $html;

	static function instance()
	{
		return new Admin();
	}

	function __construct()
	{
		$this->html = Html::instance();
		$this->html->setCss('/assets/system/news/css/admin_styles.css');
		$this->html->setCss('/assets/modules/amocrm/css/amo_style.css');
		$this->html->setJs('/assets/vendors/datatables/js/jquery.dataTables.min.js');
		$this->html->setJs('/assets/vendors/bootstrap-datepicker/js/bootstrap-datepicker.js');
		$this->html->setJs('/assets/vendors/bootstrap-datepicker/js/bootstrap-datepicker.ru.min.js');
		$this->html->setJs('/assets/system/news/js/news_datatable.js');
		$this->html->setJs('/assets/modules/amocrm/js/amo_crm.js');
		parent::__construct($this->model);
	}

	public function actionIndex()
	{

		$data = AmoCrmSettings::instance()->get();

		$this->html->title = 'Настройка подключения AmoCRM';
		$this->html->content = $this->render('Index.php', $data);
		$this->showTemplate();
	}

	public function actionSettings()
	{
		$data = $_POST;

		if (AmoCrmSettings::instance()->save($data)) {
            return json_encode(['error' => 0, 'message'=>'Успешное сохранение']);
        } else {
            return  json_encode(['error' => 1, 'message'=>'Ошибка при сохранении']);
        }
	}

	public function actionFields()
	{

		$apiClient = Amo::instance()->authCrm();

		$customFieldsService = $apiClient->customFields(EntityTypesInterface::LEADS);
		$category = Amo::LEADS_FIELD;
		$data_leads = $customFieldsService->get()->toArray();
echo"<pre>";print_r($category);echo"</pre>";
		$data = AmoCrmFields::instance()->select('*')->where('category','=',$category)->getAll();
    	$data_items = AmoCRMFieldsItems::instance()->getAll();
echo"<pre>";print_r($data_items);echo"</pre>";
die('ok');
		$arr_amo = [];
		foreach ($data_leads as $row) {
			$arr_amo[] .= $row['id'];
		}

		$arr_data = [];
		foreach ($data as $row) {
			$arr_data[] .= $row->id_field;
		}

		$arr_res = array_diff($arr_data, $arr_amo);
		if (!empty($arr_res)) {
			(new \modules\amocrm\models\AmoCrmFields)->DeleteField($arr_res);
		}

		$dataNew = [];
		foreach ($data_leads as $Row) {
			$dataNew['id'] = '';
			foreach ($data as $Row_data) {

				if (isset($Row_data->id) && $Row_data->id_field == $Row['id']) {
					$dataNew['id'] = $Row_data->id;
				}
			}
			$dataNew['name'] = $Row['name'];
			$dataNew['type'] = $Row['type'];
			$dataNew['is_api_only'] = $Row['is_api_only'];
			if ($Row['is_api_only'] == null) {
				$dataNew['is_api_only'] = 2;
			}
			$dataNew['id_field'] = $Row['id'];
			$dataNew['category'] = $category;
			$dataNew['code'] = $Row['code'];

			AmoCrmFields::instance()->save($dataNew);

			if (!empty($Row['enums'])) {
				foreach ($Row['enums'] as $item) {
					$dataItem = [];
					foreach ($data_items as $row_item)
						if ($item['id'] == $row_item['id_field']) {
							$dataItem['id'] = $row_item['id'];
							break;
						}
					$dataItem['id_field'] = $item['id'];
					$dataItem['p_id'] = $Row['id'];
					$dataItem['value'] = $item['value'];

					AmoCRMFieldsItems::instance()->save($dataItem);
				}
			}
		}

		$customFieldsService = $apiClient->customFields(EntityTypesInterface::CONTACTS);
		$category = Amo::CONTACT_FIELD;
		$data_contacts = $customFieldsService->get()->toArray();
		$ContactsInfo = AmoCrmFields::instance()->getAllByParams(['category' => $category]);

		$arr_amo = [];
		foreach ($data_contacts as $row) {
			$arr_amo[] .= $row['id'];
		}

		$arr_data = [];
		foreach ($ContactsInfo as $row) {
			$arr_data[] .= $row['id_field'];
		}

		$arr_res = array_diff($arr_data, $arr_amo);
		if (!empty($arr_res)) {
			AmoCrmFields::DeleteField($arr_res);
		}

		$dataNew = [];
		foreach ($data_contacts as $Row) {
			$dataNew['id'] = '';
			foreach ($ContactsInfo as $Row_Info) {
				if (isset($Row_Info['id']) && $Row_Info['id_field'] == $Row['id']) {
					$dataNew['id'] = $Row_Info['id'];
				}
			}
			$dataNew['name'] = $Row['name'];
			$dataNew['type'] = $Row['type'];
			$dataNew['is_api_only'] = $Row['is_api_only'];
			if ($Row['is_api_only'] == null) {
				$dataNew['is_api_only'] = 2;
			}
			$dataNew['id_field'] = $Row['id'];
			$dataNew['category'] = $category;
			$dataNew['code'] = $Row['code'];

			AmoCrmFields::instance()->save($dataNew);
		}

		$data = AmoCrmFields::instance()->getAll();

		$this->html->title = 'Поля AmoCRM';
		$this->html->content = $this->render('fields/fields_list.php', $data);
		$this->showTemplate();
	}

	public function actionAddField()
	{
		$category = FiealdsHelper::$category;
		$field_type = FiealdsHelper::$field_type;

		if (!empty($_POST)) {
			$apiClient = Amo::instance()->authCrm();

			$leadsCfService = $apiClient->customFields(EntityTypesInterface::LEADS);

			if ($_POST['category'] == 2) {
				$leadsCfService = $apiClient->customFields(EntityTypesInterface::CONTACTS);
			}

			if (isset($_POST['is_api_only'])) {
				$api = true;
				$_POST['is_api_only'] = 1;
			} else {
				$api = false;
				$_POST['is_api_only'] = 2;
			}

			if ($_POST['type'] == 'text') {
				$textCfModel = new TextCustomFieldModel();
				$textCfModel->setName($_POST['name']);
				$textCfModel->setIsApiOnly($api);

				/** @var TextCustomFieldModel $textCfModel */
				$leadsCfService->addOne($textCfModel);

				$leadsCfCollection = $leadsCfService->get();
				$leadsCfModelNew = $leadsCfCollection->getBy('name', $_POST['name']);
				$lead = $leadsCfModelNew->toArray();

			} elseif ($_POST['type'] == 'numeric') {
				$numericCfModel = new NumericCustomFieldModel();
				$numericCfModel->setName($_POST['name']);
				$numericCfModel->setIsApiOnly($api);

				/** @var NumericCustomFieldModel $numericCfModel */
				$leadsCfService->addOne($numericCfModel);

				$leadsCfCollection = $leadsCfService->get();
				$leadsCfModelNew = $leadsCfCollection->getBy('name', $_POST['name']);
				$lead = $leadsCfModelNew->toArray();

			}

			$dataNew['id'] = '';
			$dataNew['name'] = $_POST['name'];
			$dataNew['type'] = $_POST['type'];
			$dataNew['id_field'] = $lead['id'];
			$dataNew['category'] = $_POST['category'];
			$dataNew['name_in_form'] = $_POST['name_in_form'];
			$dataNew['is_api_only'] = $_POST['is_api_only'];
			AmoCrmFields::instance()->save($dataNew);

			header("Location: /amocrm/admin/fields");
			exit;
		}

		$this->html->title = 'Добавление Поля в AmoCRM';
		$this->html->content = $this->render('fields/add_form.php', ['category' => $category, 'field_type' => $field_type]);
		$this->showTemplate();
	}

	public function actionEdit($id)
	{
		$field = AmoCrmFields::instance()->getOne($id);

		$apiClient = Amo::instance()->authCrm();
		$leadsCfService = $apiClient->customFields(EntityTypesInterface::LEADS);

		if ($field['category'] == 2) {
			$leadsCfService = $apiClient->customFields(EntityTypesInterface::CONTACTS);
		}

		$leadsCfCollection = $leadsCfService->get();
		$leadsCfModel = $leadsCfCollection->getBy('name', $field['name']);
		if (empty($_POST)) {
			$lead = $leadsCfModel->toArray();
		}

		if (!empty($_POST)) {
			if (isset($_POST['is_api_only'])) {
				$api = true;
				$_POST['is_api_only'] = 1;
			} else {
				$api = false;
				$_POST['is_api_only'] = 2;
			}

			if ($field['type'] != $_POST['type']) {
				$leadsCfService->deleteOne($leadsCfModel);
				if ($_POST['type'] == 'text') {
					$textCfModel = new TextCustomFieldModel();
					$textCfModel->setName($_POST['name']);
					$textCfModel->setIsApiOnly($api);

					/** @var TextCustomFieldModel $textCfModel */
					$leadsCfService->addOne($textCfModel);

					$leadsCfCollection = $leadsCfService->get();
					$leadsCfModelNew = $leadsCfCollection->getBy('name', $_POST['name']);
					$lead = $leadsCfModelNew->toArray();

				} elseif ($_POST['type'] == 'numeric') {
					$numericCfModel = new NumericCustomFieldModel();
					$numericCfModel->setName($_POST['name']);
					$numericCfModel->setIsApiOnly($api);

					/** @var NumericCustomFieldModel $numericCfModel */
					$leadsCfService->addOne($numericCfModel);

					$leadsCfCollection = $leadsCfService->get();
					$leadsCfModelNew = $leadsCfCollection->getBy('name', $_POST['name']);
					$lead = $leadsCfModelNew->toArray();

				}

				$dataNew['id'] = $_POST['field_id'];
				$dataNew['name'] = $_POST['name'];
				$dataNew['type'] = $_POST['type'];
				$dataNew['id_field'] = $lead['id'];
				$dataNew['name_in_form'] = $_POST['name_in_form'];
				$dataNew['is_api_only'] = $_POST['is_api_only'];
				AmoCrmFields::instance()->save($dataNew);

				$field = AmoCrmFields::instance()->getOne($_POST['field_id']);

			} else {
				$leadsCfCollection = $leadsCfService->get();
				$leadsCfModel = $leadsCfCollection->getBy('name', $field['name']);
				$leadsCfModel->setName($_POST['name']);
				$leadsCfModel->setIsApiOnly($api);
				$leadsCfService->updateOne($leadsCfModel);

				$leadsCfModelNew = $leadsCfCollection->getBy('name', $_POST['name']);
				$lead = $leadsCfModelNew->toArray();

				$dataNew['id'] = $_POST['field_id'];
				$dataNew['name'] = $_POST['name'];
				$dataNew['type'] = $_POST['type'];
				$dataNew['name_in_form'] = $_POST['name_in_form'];
				$dataNew['is_api_only'] = $_POST['is_api_only'];
				AmoCrmFields::instance()->save($dataNew);

				$field = AmoCrmFields::instance()->getOne($_POST['field_id']);
			}
		}

		$field_type = FiealdsHelper::$field_type;

		if (!empty($_POST) && !isset($_POST['sendAndStop_no'])) {
			header("Location: /amocrm/admin/fields");
			exit;
		} else {
			$this->html->title = 'Редактирование Поля AmoCRM';
			$this->html->content = $this->render('fields/edit_form.php', ['lead' => $lead, 'field' => $field, 'field_type' => $field_type]);
			$this->showTemplate();
		}

	}

	public function actionDelete($id)
	{
		$field = AmoCrmFields::instance()->getOne($id);

		$apiClient = Amo::instance()->authCrm();
		$CfService = $apiClient->customFields(EntityTypesInterface::LEADS);

		if ($field['category'] == 2) {
			$CfService = $apiClient->customFields(EntityTypesInterface::CONTACTS);
		}

		$leadsCfCollection = $CfService->get();

		$fieldToDelete = $leadsCfCollection->getBy('name', $field['name']);
		$data = $fieldToDelete->toArray();

		if ($fieldToDelete) {
			AmoCrmFields::DeleteField([$data['id']]);

			$CfService->deleteOne($fieldToDelete);
		}


		header("Location: /amocrm/admin/fields");
		exit;
	}

	function showTemplate($layout = '@admin')
	{
		$this->html->setTemplate($layout);
		$this->html->renderTemplate()
				   ->show();
	}

}