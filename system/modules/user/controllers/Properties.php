<?php
/**
 * Create by e-Mind Studio
 * User: Женя
 * Date: 28.03.2018
 * Time: 10:26
 */

namespace modules\user\controllers;


use core\Controller;
use core\Html;
use modules\user\helpers\UserslHelper;
use modules\user\models\Property;
use modules\user\models\Property_value;
use modules\user\models\USER;

/**
 * @property-read Property $propertyModel
 * Class Properties
 * @package modules\user\controllers
 */
class Properties extends Controller
{
    private $propertyModel;

    public function __construct($model = null)
    {
        $this->setPropertyModel(new Property());
        parent::__construct($model);
    }

    public function actionIndex()
    {

        Html::instance()->title = "Настройки пользователей";
        Html::instance()->setCss('/assets/vendors/datatables/css/dataTables.bootstrap.min.css');
        Html::instance()->setJs("/assets/vendors/datatables/js/jquery.dataTables.min.js");
        Html::instance()->setJs("/assets/vendors/datatables/js/dataTables.bootstrap.js");
        Html::instance()->setJs("/assets/vendors/ace-builds-master/src/ace.js");
        Html::instance()->setJs("/assets/modules/user/js/properties.js");
        Html::instance()->setCss("/assets/modules/user/css/userProperties.css");
        Html::instance()->content = $this->render('properties/index.php', ['properties' => $this->propertyModel->getAll()]);
        Html::instance()->renderTemplate("@admin")->show();
    }

    public function actionList()
    {
        $items      = [];
        $properties = $this->propertyModel->like('name', "%" . $_POST['search']['value'] . "%")->orLike('title', "%" . $_POST['search']['value'] . "%")->orLike('description', "%" . $_POST['search']['value'] . "%")->getAll('array');
        if (!empty($properties)) {
            foreach ($properties AS $item) {
                $record = [
                    'DT_RowId'    => "property_" . $item['id'],
                    'id'          => $item['id'],
                    'name'        => $item['name'],
                    'title'       => $item['title'],
                    'description' => $item['description'],
                    'type'        => UserslHelper::getPropertyTypes()[$item['type']],
                    'buttons'     => $this->render('properties/control_buttons.php', ['id' => $item['id']]),
                ];

                $items[] = $record;
            }
        }
        $result = [
            "draw"            => $_POST['draw'],
            "recordsTotal"    => count($properties),
            "recordsFiltered" => count($properties),
            "data"            => $items
        ];
        echo json_encode($result);

    }

    public function actionAdd()
    {
        echo $this->render('properties/form.php', ['property' => $this->propertyModel->factory()]);
    }

    public function actionEdit($id)
    {
        echo $this->render('properties/form.php', ['property' => $this->propertyModel->factory($id)]);
    }

    public function actionSave()
    {
        $this->propertyModel->factory($_POST['id'])->fill($_POST);
        $this->propertyModel->owner_id = USER::current()->id;
        if ($this->propertyModel->save()) {
            echo json_encode(['status' => 'OK']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $this->propertyModel->getError()]);
        };
    }

    public function actionCheckUnique()
    {
        if (!empty($_POST)) {
            $count_id = $this->propertyModel->where(['name' => $_POST['name'], 'owner_id' => USER::current()->id])->count('id', 'count_id')->getOne()->count_id;
            if ($this->propertyModel->clear()->getOne($_POST['id']) && $this->propertyModel->clear()->getOne($_POST['id'])->name == $_POST['name']) {
                echo "OK";
            } elseif ($count_id == 0) {
                echo "OK";
            } else {
                echo "NotUnique";
            }
        }
    }

    public function actionDelete()
    {
        if ($_POST['id']) {
            $this->propertyModel->delete($_POST['id']);
            echo json_encode(['status' => 'OK']);
        }
    }

    /**
     * @param Property $propertyModel
     */
    private function setPropertyModel(Property $propertyModel)
    {
        $this->propertyModel = $propertyModel;
    }
}