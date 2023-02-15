<?php
$r = false;
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $data['m'] . "/rights.php")) {
    $r = include_once($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $data['m'] . "/rights.php");
}



if ($r) {
    $r[$data['c']] = ['role' => ['admin']];

    ?>
    return
    <?php
        ob_start();
        var_export($r);
        $end = ob_get_contents();
        ob_end_clean();
        $end = str_replace("array (",'[', $end);
        $end = str_replace("array(",'[', $end);
        $end = str_replace(")",']',$end);
        echo $end;
    ?> ; <?php


} else {
    ?>
    return [
    '<?= $data['c'] ?>' => [ // контролер
    'role' => ['admin'],
    // кому разрешен доступ к контролеру
    ],
    ];
    <?php
}
?>