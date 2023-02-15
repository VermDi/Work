<?php
/**
 * Create by e-Mind Studio
 * User: E_dulentsov
 * Date: 18.05.2017
 * Time: 9:30
 */

namespace core\interfaces;


interface Filesystem
{
    /**
     * @param $file - принимает путь до файла
     * @return bool - возвращает наличие или отстуствие файла и записывает ошибку
     */
    function isFile($file);

}