<?php
if(is_array($data) && count($data)>0){

    $html='';
    $maxKey = max(array_keys($data));

    $html .= '<ul class="breadcrumb">';
    foreach ($data as $level => $BreadcrumbRow){
        if($maxKey!=$level){
            $html .= '<li><a href="/'.$BreadcrumbRow->url.'">'.$BreadcrumbRow->name.'</a></li>';
        } else {
            $html .= '<li class="active">'.$BreadcrumbRow->name.'</li>';
        }
    }
    $html .= '</ul>';
    echo $html;
}
