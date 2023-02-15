<?php
/**
 * Created by PhpStorm.
 * User: dulentcov-smishko
 * Date: 04.03.2019
 * Time: 17:10
 */

namespace modules\news\repositories;


use modules\news\models\Article;

/**
 * Источнки одной правды, статьи получаем только здесь.
 *
 * Class Articles
 *
 * @package modules\news\repositories
 */
class Articles
{
    /**
     * Получение всех активных статей раздела
     *
     * @param int $catId
     * @return bool|mixed|\PDOStatement|\stdClass|string
     */
    public static function getActiveArticlesInCategory($catId = 0, $limit = false)
    {
        $ar = Article::instance()
            ->where('visible', '=', 1)
            ->where('deleted', '=', 0)
            ->where('categories_id', '=', $catId)
            ->whereRaw('(data_end > NOW() or data_end is null) ')
            ->whereRaw('(data_create <= NOW() or data_create is null) ')
            ->orderBy('data_create DESC');
        if ($limit) {
            $ar->limit($limit);
        }
        return $ar->getAll();
    }

    /**
     * Получение ВСЕХ статей раздела
     * @param int $catId
     * @return bool|mixed|\PDOStatement|\stdClass|string
     */
    public static function getAllArticlesInCategory($catId = 0)
    {
        return Article::instance()
            ->where('deleted', '=', 0)
            ->where('categories_id', '=', $catId)
            ->orderBy('data_create DESC')
            ->getAll();
    }

    /**
     * Получение оглавления всех статей раздела
     * @param int $catId
     * @return bool|mixed|\PDOStatement|\stdClass|string
     */
    public static function getPreviewActiveArticlesInCategory($catId = 0)
    {
        return Article::instance()
            ->select(['name', 'id', 'alias', 'data_create'])
            ->where('visible', '=', 1)
            ->where('deleted', '=', 0)
            ->where('categories_id', '=', $catId)
            ->whereRaw('(data_end > NOW() or data_end is null) ')
            ->whereRaw('(data_create <= NOW() or data_create is null) ')
            ->orderBy('data_create DESC')
            ->getAll();
    }

    /**
     * Возвращает активную статью
     * @param $articleId
     * @return bool|mixed|\PDOStatement|\stdClass|string
     */
    public static function getActiveArticle($alias = false, $articleId = false)
    {
        $ar = Article::instance()
            ->where('visible', '=', 1)
            ->where('deleted', '=', 0);
        if ($alias) {
            $ar->where('alias', '=', $alias);
        }
        if ($articleId) {
            $ar->where('id', '=', $articleId);
        }
        $ar->whereRaw('(data_end > NOW() or data_end is null) ')
            ->whereRaw('(data_create <= NOW() or data_create is null) ')
            ->getOne();

        if (empty($ar->id)) {
            return false;
        }
        return $ar;
    }


}