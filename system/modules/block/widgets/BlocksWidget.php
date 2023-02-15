<?php
namespace modules\block\widgets;

use modules\block\models\mBlocks;

class BlocksWidget {

    public $ContentInsertBlocksData='';

    public static function instance(){
        return new BlocksWidget;
    }

    /**
     * Если найден получаем блок из файла
     * @param $code
     * @return string
     */
    public function getBlocksFile($code){
        $html = '';

        // Получаем get дынные block_name?status=1&test=2
        $getData=[];
        $codeArr=explode('?',$code);
        $code=$codeArr[0];
        if(isset($codeArr[1])){
            $getData = $this->parseGetCode($codeArr[1]);
        }

        $FilesBlocks = mBlocks::instance()->getFilesBlocks();
        if(isset($FilesBlocks[$code]['patch'])){
            $html = mBlocks::instance()->getBlockBuffer($FilesBlocks[$code]['patch'], $getData);
        }
        return $html;
    }

    /**
     * Разбиваем get данные из строки в мссив
     * @param $getcode
     * @return array
     */
    public function parseGetCode($getcode){
        // редактор правит
        $getcode = str_replace("&amp;", "&", $getcode);

        $GetData=[];
        $ValuesArr=explode('&',$getcode);
        foreach ($ValuesArr as $ValuesRow){
            $ValuesData=explode('=',$ValuesRow);
            if(!empty($ValuesData[0]) && !empty($ValuesData[1])){
                $GetData[$ValuesData[0]]=$ValuesData[1];
            }

        }
        return $GetData;
    }

    /**
     * Если найден блок в файле то получаем его
     * @param $code
     * @return string
     */
    public function getAnyBlock($code){
        /*
         * проверим файл
         */
        if(empty($html)){
            $html = $this->getBlocksFile($code);
        }

        return $html;
    }

    /**
     * Отдаем контент, происходит замена блоков и отдаем назад
     * @param $Content
     * @return mixed
     */
    public function getContentInsertBlocks($Content){
        $this->ContentInsertBlocksData=$Content;
        $this->setContentBlocks($Content);
        echo $this->ContentInsertBlocksData;
    }

    /**
     * Отдаем контент, происходит замена блоков и отдаем назад,
     * зацикливаем, пока всек блоки не обработаем, блоки в блоках например
     * @param $Content
     * @return mixed
     */
    public function setContentBlocks($Content){
        $BlocksArr = $this->getBlocksArr($Content);

        foreach ($BlocksArr as $BlockRow){
            $blockHtml = $this->getAnyBlock($BlockRow['code']);
            if(!empty($blockHtml)){
                $Content = str_replace($BlockRow['code_replace'], $blockHtml, $Content);
            }
        }

        /*
         * Если контент окончателе то отдаем
         */
        if($this->ContentInsertBlocksData==$Content){
            return $Content;
        } else {
            $this->ContentInsertBlocksData=$Content;
            $this->setContentBlocks($Content);
        }
    }


    /**
     * Получить массив для блоков в контенте
     * @param $Content
     * @param string $beforeStr
     * @param string $AfterStr
     * @return array
     */
    public function getBlocksArr($Content, $beforeStr = '{!#', $AfterStr = '#!}') {
        $BlocksArr=[];
        $pattern = "/".$beforeStr."[^}]*".$AfterStr."/";
        preg_match_all($pattern, $Content, $Arr);
        if(isset($Arr[0][0])){
            foreach ($Arr[0] as $code_replace){

                $code = str_replace($beforeStr, "", $code_replace);
                $code = str_replace($AfterStr, "", $code);

                $BlocksArr[]=[
                    'code_replace'=>$code_replace,
                    'code'=>$code,
                ];
            }
        }
        return $BlocksArr;
    }

}