<?php



$txt_c = "<?php
namespace modules\\" . $data['GKGName'] . "\\controllers;
use core\\Controller;
use core\\Html;
use core\\Tools;
use core\\Response;
use core\\Errors;
class " . $data['conName'] . " extends Controller
{
    function __construct()
	{
	    parent::__construct();
		\$this->model         = new \\modules\\" . $data['GKGName'] . "\\models\\" . $data['modName'] . "();
			
	}
	/*
	* Первый экшен, по умолчанию берет данные по AJAX из actionGetList  
	*/
	public function actionIndex()
    {
        html()->setJs(\"/assets/vendors/datatables/js/jquery.dataTables.min.js\");
        html()->setCss(\"/assets/vendors/datatables/css/jquery.dataTables.min.css\");
        html()->setJs(\"/assets/vendors/e-mindhelpers/EM.js\");
        html()->setJs(\"/assets/vendors/e-mindhelpers/ContentFormBuilder.js\");
        
        /* -----------------------Укажите темплейт списка----------------------- */
        html()->content = \$this->render(\"" . $data['temListName'] . ".php\");
        
        html()->renderTemplate(/* для админки @admin, или ваш темлейт */)->show(); //будет использован ваш основной дизайн
        
        
    }

    public function actionGetlist(\$ajax=false)
    {
        /*
        * для передачи по ajax в формет json
        * если нужно огрничить объем данных, то используйте
        * \$this->>model->select([поля...])
        */
        \$res = \$this->model->getAll();
        if (\$ajax!==false){
            echo json_encode(\$res);
        } else {
            \$this->render(/* ВАШ ШАБЛОН СПИСКА */);
        }
        die();
    }

    public function actionForm(\$id = false, \$ajax=false)
    {
        /* ----------------------- Укажите темлейт формы ----------------- */
         if (\$ajax!=false) {
            echo \$this->render(\"" . $data['temName'] . ".php\", \$this->model->factory(\$id));
        } else {
            html()->content = \$this->render(\"" . $data['temName'] . ".php\", \$this->model->factory(\$id));
            html()->renderTemplate()->show();
        }
        die();
    }

    public function actionSave()
    {
    
        if (\$this->model->factory()->fill(\$_POST)->save()) {
            Response::ajaxSuccess(\$this->model->getOne(\$this->model->insertId()));
        } else {
             Response::ajaxError(\$this->model->error());
        }
    }
    
    /**
     * Удаление данных
     * @param \$id
     * @throws \Exception
     */

    public function actionDelete(\$id, \$ajax = false)
    {
        if (intval(\$id) < 1) {
            Errors::ajaxError('НЕ ПЕРЕДА КЛЮЧ');
        }
        if (\$this->model->delete(\$id)) {
            if (\$ajax) {
                die(json_encode(['success' => 1, 'msg' => 'OK']));
            } else {
                header(\"Location: \" . \$_SERVER['HTTP_REFERER']);
            }
        } else {
            Errors::ajaxError('не смог удалить');
        }
    }
    
    /**
     * Экспорт в CSV
     */
    public function actionToCsv()
    {
        \$arr = \$this->model->getAll();
        array_walk_recursive(\$arr, function (&\$value, \$key) {
            if (is_string(\$value)) {
                \$value = iconv(\"UTF-8\", \"CP1251\", \$value);
            }
            if (is_object(\$value)) {
                foreach (\$value as \$k => \$v) {
                    \$value->\$k = iconv(\"UTF-8\", \"CP1251\", \$v);
                }
            }
        });

        Tools::array2csvFile(\$arr, \$this->model->getColumnsName(), 'all');
    }

    public function getFromCsv()
    {
        Html::instance()->content = \$this->render('/LoadFileForm.php');
        Html::instance()->renderTemplate()->show(); //для админа шаблон @admin
    }

    /**
     * Правка из CSV
     */
    public function postFromCsv()
    {
        \$arr = \core\Tools::csv2Array(\$_FILES['CSV']['tmp_name'], \$this->model->clear()->factory()->getColumnsName());
        foreach (\$arr as \$item) {
            \$this->model->clear()->factory()->fill(\$item)->save();
        }
        header(\"Location: /".$data['GKGName']."/admin\");
    }

}";
echo $txt_c;
