<?php
/**
 * Create by e-Mind Studio
 * User: Женя
 * Date: 16.04.2017
 * Time: 14:10
 */

namespace core\interfaces;


interface Migrations
{
    public function Migrate();

    public function RollBack();

}