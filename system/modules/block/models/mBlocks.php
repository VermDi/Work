<?php
namespace modules\block\models;


/**
 * Class mBlocks
 * @package modules\block\models
 */
class mBlocks
{

    public $table = 'blocks';
    public $FilesBlocksPatch = __DIR__.'/../templates/blocksphp/';

    public static function instance(){
        return new mBlocks;
    }

    public function getFilesBlocks(){
        $FilesBlocks=[];
        if(file_exists($this->FilesBlocksPatch)){
            $files1 = scandir($this->FilesBlocksPatch);
            foreach ($files1 as $file){
                if($file!='.' && $file!='..'){
                    $fileExpansion = mBlocks::instance()->getExpansion($file);
                    if($fileExpansion=='php'){
                        $name = basename($file, ".php");
                        $FilesBlocks[$name]=[
                            'code'=>$name,
                            'file'=>$file,
                            'patch'=>$this->FilesBlocksPatch.$file,
                        ];
                    }
                }
            }
        }
        return $FilesBlocks;
    }

    public function getBlockBuffer($patch, $data)
    {
        ob_start();                                //  Let's start output buffering.
        include $patch;
        $contents = ob_get_contents();             //  Instead, output above is saved to $contents
        ob_end_clean();
        return $contents;
    }

    /**
     * Получить расширение по имени файла
     * @param $name_file
     * @return mixed
     */
    public function getExpansion($name_file){
        preg_match('/.+\.(\w+)$/xis', $name_file, $pocket);
        return mb_strtolower($pocket[1]);
    }

}