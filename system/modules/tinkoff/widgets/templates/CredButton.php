<?
$dataSettings = \modules\tinkoff\models\TinkoffSettingsButton::instance()->getOne();
$items = $data;
//echo "<pre>";
//print_r($dataSettings);
//echo "</pre>";
echo "<pre>";
print_r($items);
echo "</pre>";
if (!empty($dataSettings)) {

    $sum = 0;

    $data[0] = ['name', 'price', 'quantity'];
    foreach ($items as $key => $value) {
        $piceItem = (int)$value['price'] * (int)$value['quantity'];
        $sum += $piceItem;
        $data[$key + 1] = [$value['name']];
        array_push($data[$key + 1], $value['price']);
        array_push($data[$key + 1], $value['quantity']);
    }

    $resultButton = "
	<script src='https://forma.tinkoff.ru/static/onlineScript.js'></script>
	<input type='hidden' id='dataArr' value='" . json_encode($data) . "'/>
	<button
	type='button'
	class='TINKOFF_BTN_YELLOW " . $dataSettings->buttonStyle . " credTinkoff'
>" . $dataSettings->buttonName . "</button>
<script>

let btnCredTinkoff = document.querySelector('.credTinkoff')

	function convertToArrayOfObjects(data) {
		let keys = data.shift(),
			i = 0, k = 0,
			obj = null,
			output = [];
	
		for (i = 0; i < data.length; i++) {
			obj = {};
	
			for (k = 0; k < keys.length; k++) {
				obj[keys[k]] = data[i][k];
			}
	
			output.push(obj);
		}
	
		return output;
	}

	function credClick() {
    
       let dataArr = JSON.parse(document.querySelector('#dataArr').value)
       let data = convertToArrayOfObjects(dataArr)

		tinkoff.createDemo(
			{
				sum: " . $sum . ",
				items: data,
				promoCode: '" . $dataSettings->promoCode . "',
				shopId: '" . $dataSettings->SHOP_ID . "',
				showcaseId: '" . $dataSettings->SHOWCASE_ID . "',
			},
			{view: '" . $dataSettings->view . "'}
		)
	}
	
	btnCredTinkoff.addEventListener('click', credClick)
	
</script>";

    echo $resultButton;
} else {
    echo 'Заполните Поля в настройках подключения Кредитования в Админ панели';
}


