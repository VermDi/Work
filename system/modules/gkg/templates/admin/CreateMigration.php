<?php $txt_c = "<?php

namespace modules\\" . $data['name'] . "\\migrations;

use core\\interfaces\\Migrations;

class Pages implements Migrations
{
    public function Migrate()
    {
        return true;
    }

    public function RollBack()
    {
        return true;
    }

}

";
echo $txt_c;
