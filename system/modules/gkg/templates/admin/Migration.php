
use core\Migration;

class m<?=$data['t'];?>_Install extends Migration
{
    public function up()
    {
        //$this->query("");
        if (class_exists('\modules\menu\services\MenuActions')) {
            \modules\menu\services\MenuActions::addInAdminMenu('/<?=$data['m'];?>/<?=$data['c'];?>', '<?=$data['c'];?>');
        }
        return true;
    }

    public function down()
    {
        //$this->query("DROP TABLE IF EXISTS ");
        return true;
    }
}