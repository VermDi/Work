<?php
include_once 'menu.php';

$phpText = '<? \modules\tinkoff\widgets\tinkoffButton::instance()->getTinkoffButton($data); ?>';
$Arrdata = "\$data = [
	[
		'name'     => 'iphone 11', 
		'price'    => 120000,
		'quantity' => 1,
	],
	[
		'name'  => 'Чехол',
		'price' => 500, 
		'quantity' => 1,
	],
	[
		'name'     => 'Зарядка для iphone 11',
		'price'    => 1500,
		'quantity' => 2,
	],
];"
?>
<div class="manual_tinkoff_cred" style="margin-bottom: 20px">
	<h4>Для того чтобы разместить кнопку на вашем проекте:</h4>
	<div class="manual_tinkoff_box">
		<p>
			<b>1)</b> Введите и сохраните все параметры <a href="/tinkoff/admin/settings" style="text-decoration:underline">Настройка кнопки кредитования</a>
		</p>
		<p>
			<b>2)</b> После настройки и сохранения: <br>
			Скопируйте данную PHP строку кода и в ставьте в нужное место на проекте. При необходимости
			отредактируйте расположение кнопки Настройками CSS.
		</p>
		<div>
			<pre class="manual_pre_item1"><?= htmlspecialchars($phpText) ?></pre>
		</div>
		<div>
			<b>3) $data</b> - Массив товаров, который мы передаем для оформления Кредитования/Рассрочки. <br>
			<b>Пример конструкции массива:</b><br>
			<div style="margin-left: 15px; margin-bottom: 10px">
				<b>name</b> - Название товара. <b>--строка--</b> <br>
				<b>price</b> - Цена за 1 ед. товара. <b>--число--</b> <br>
				<b>quantity</b> - Количество данного товара. <b>--число--</b> <br>
			</div>
		</div>
		<div>
			<? echo "<pre>";print_r($Arrdata);echo "</pre>"; ?>
		</div>
	</div>
</div>
