
<div class="item active"><?

    $counter = 0;
    $content="";
    foreach ($data as $k => $v) {
        $counter++;
        $content .= (($counter > 1) ? '
    <div class="rozrez"></div>
    ' : '') . '
    <div class="block_' . $counter . '"><a href="/news/' . $v->alias . '">' . $v->name . '</a>' .
            $v->short_article . '
    </div>
    ';
    }
    echo $content;
    ?>
</div>