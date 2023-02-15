<?php
include_once 'menu.php';

$data= [['name'=> 'iphone 11', 'price'=> 120000, 'quantity'=> 1], ['name'=> 'Чехол', 'price'=> 500, 'quantity'=> 1], ['name'=> 'Зарядка', 'price'=> 1500, 'quantity'=> 2], ['name'=> 'imac', 'price'=> 175300, 'quantity'=> 1]];

\modules\tinkoff\widgets\tinkoffButton::instance()->getTinkoffButton($data);
