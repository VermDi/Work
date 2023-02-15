<?php
$txt = "<form action = \"/".$data['GKGName']."/admin/save\" name=\"ПОСМОТРИ А ЛИСТИНГ темплейте (let dt = new FormData .... ) или укажи свое\" method = \"post\" class = \"form-horizontal\" enctype=\"multipart/form-data\">" . PHP_EOL;
$final_array = array();
echo "<!--";
print_r($data);
echo "-->";
echo <<<HTML
<div class="panel">
    <div class="panel-heading">НАЗВАНИЕ ВАШЕГО РАЗДЕЛА</div>
    <div class="panel-body">
HTML;

foreach ($data as $k => $v) {
    if (strpos($k,"-comment")){continue;}
    if ($v == "Input") {
        $txt .= "<label for='" . $k . "'>".(isset($data[$k."-comment"])?$data[$k."-comment"]:"")."</label>". PHP_EOL;
        $txt .= "<input type='text' name='" . $k . "' class='form-control' value='<?=\$data->".$k.";?>'>" . PHP_EOL;
        $final_array[$k] = 1;
    }
    if ($v == "Textarea") {
        $txt .= "<label for='" . $k . "'>".(isset($data[$k."-comment"])?$data[$k."-comment"]:"")."</label>". PHP_EOL;
        $txt .= "<textarea name='" . $k . "' class='form-control'><?=\$data->".$k.";?></textarea>" . PHP_EOL;
        $final_array[$k] = 1;
    }
    if ($v == "Checkbox") {
        $txt .= "<label for='" . $k . "'>".(isset($data[$k."-comment"])?$data[$k."-comment"]:"")."</label>". PHP_EOL;
        $txt .= "<input type='checkbox' name='" . $k . "'>" . PHP_EOL;
        $final_array[$k] = 1;
    }
    if ($v == "Img") {
        $txt .= "<label for='" . $k . "'>".(isset($data[$k."-comment"])?$data[$k."-comment"]:"")."</label>". PHP_EOL;
        $txt .= "<input type='file' name='" . $k . "'>" . PHP_EOL;
        $final_array[$k] = 1;
    }
    if ($v == "Radio") {
        $txt .= "<label for='" . $k . "'>".(isset($data[$k."-comment"])?$data[$k."-comment"]:"")."</label>". PHP_EOL;
        $txt .= "<input type='radio' name='" . $k . "' <?= (\$data->".$k."==1)?\"checked\":\"\"; ?>>" . PHP_EOL;
        $final_array[$k] = 1;
    }
    if ($v == "Select") {
        $txt .= "<label for='" . $k . "'>".(isset($data[$k."-comment"])?$data[$k."-comment"]:"")."</label>". PHP_EOL;
        $txt .= "<select name='" . $k . "'><option value='1'>1</option></select>" . PHP_EOL;
        $final_array[$k] = 1;
    }
    //print_r($v[0]); echo "<hr>";
}
//$txt .= "<button class='btn btn-success' style='width: 100%; margin-top: 25px;' id='sendAndStop' type='submit'>Сохранить</button>";

echo $txt . "</form></div></div>";
