<?php
$rezult = \core\Db::instance()
    ->query("SHOW FULL COLUMNS from " . htmlspecialchars($_POST['GKGtable']))
    ->fetchAll();
$model_txt = "<?php
namespace modules\\" . $data['GKGName'] . "\\models;

use core\\Model;

/**
 * Class " . $data['modName'] . "\r\n";
foreach ($rezult as $k => $v) {
    $model_txt .= "* @property string " . $v[0] . " - " . $v['Comment'] . "\r\n ";
}

$model_txt .= " 
*/
class " . ucfirst($data['modName']) . " extends Model
{ 
    public \$table = '" . $data['GKGtable'] . "';
    public function factory(\$id=false)
    {
        if (\$id == false or !\$this->getOne(\$id)) 
        {";
            foreach ($rezult as $k => $v) {
                if ((strpos(strtolower($v[1]),"int") or strpos(strtolower($v[1]),"float")) and strtolower($v[3])=='no')
                {
                    $model_txt .= "            \$this->" . $v[0] . " = 0; \r\n";
                } elseif (strtolower($v[3])=='yes')
                {
                    $model_txt .= "            \$this->" . $v[0] . " = 'null'; \r\n";
                } elseif (strpos(strtolower($v[1]),"varchar") or strpos(strtolower($v[1]),"text"))
                {
                    $model_txt .= "            \$this->" . $v[0] . " = \"\"; \r\n";
                } elseif (strpos(strtolower($v[1]),"date") or strpos(strtolower($v[1]),"time"))
                {
                    $model_txt .= "            \$this->" . $v[0] . " = \"NOW()\"; \r\n";
                }  else {
                    $model_txt .= "            \$this->" . $v[0] . " = \"\"; \r\n";
                }


            }
            $model_txt .= " 
        }
        return \$this;
    }	
}";

echo $model_txt;
