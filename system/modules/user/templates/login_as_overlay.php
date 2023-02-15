<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 26.06.2017
 * Time: 12:59
 */
?>
<div style="position:fixed; top:0; color: #fff; opacity: 0.7; background:#666; width:100%; padding: 5px; z-index:9999">
    Выполнен вход от имени <strong><?=\modules\user\models\USER::current()->email;?></strong>
    <a class="btn btn-xs btn-danger" href="/user/loginback">прекратить сеанс</a>
    <div class="clearfix"></div>
</div>
