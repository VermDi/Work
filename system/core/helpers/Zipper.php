<?php
/**
 * Create by e-Mind Studio
 * User: dulentcov-smishko
 * Date: 17.09.2018
 * Time: 17:43
 *
 * Пример упаковки в АРХИВ
 * $ziper = new Zipper();
 *  $ziper->make(__DIR__ . "/../test.zip")
 *         ->add($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/admin")
 *         ->add(__DIR__ . "/../boot.php")
 *         ->add(__DIR__ . "/../../block")
 *         ->end();
 *
 *
 * Если необходимо добавить в некую папку, предусмотрен метод to
 *   $ziper->make(__DIR__ . "/../test.zip")
 *                   //в корне будет system и уже в ней папка модуля admin
 *         ->in('system')->add($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/admin")
 *                  //добавится в корень архива
 *         ->add(__DIR__ . "/../boot.php")
 *                  //в корне создатся папка www и в ней положится папка block
 *         ->in('www')->add(__DIR__ . "/../../block")
 *         ->end();
 *
 * Пример распаковки из АРХИВА
 * $ziper->unzip(__DIR__ . "/../test.zip", __DIR__ . "/../test23/" , true);
 *
 */

namespace core\helpers;

class Zipper
{
    private $temporaryDirectory = null;
    private $archive = null;
    private $files = [];
    private $skip = [];
    private $primaryFolder = null;
    private $to = '';

    public function __construct()
    {
        if (!class_exists('ZipArchive')) {
            throw new \Exception('For the class to work, you need an enabled php module "zip"');
        }
        if ($this->temporaryDirectory == null) {
            $this->temporaryDirectory = _ROOT_PATH_.DIRECTORY_SEPARATOR."temp";
        }

        if (!file_exists($this->temporaryDirectory)) {
            mkdir($this->temporaryDirectory, '0777');
        } else {
            chmod($this->temporaryDirectory, '0777');
        }
    }

    /**
     * Принимает путь до создаваемого файла
     * @param $file - путь до файла
     * @return $this
     */
    public function make($file)
    {
        if (substr($file, -4) != '.zip') {
            throw new \Exception('Must bee zip file');
        };
        $this->archive = $file;
        return $this;
    }

    /**
     * Базовая проверка файла (папки)
     * @param $f
     * @return mixed
     * @throws \Exception
     */
    private function checkFiles($f)
    {
        if (!is_string($f)) {
            throw new \Exception('Only the path at string');
        }
        if (!file_exists($f)) {
            $this->skip[] = $f;
        }
        return $f;
    }

    /**
     *  Вспомогательная функция прохода по папкам
     * @param bool $folder
     * @param null $primaryFolder
     * @return $this
     * @throws \Exception
     */
    private function open_folder($folder = false, $primaryFolder = null)
    {
        if ($primaryFolder == null) {
            $this->primaryFolder = $folder;
        }
        if (!$path = scandir($folder)) {
            throw new \Exception("Directory scan failed: " . $folder);
        }
        if ($path != false) {
            foreach ($path as $k => $v) {
                if ($v[0] != '.' and $v[0] != '..') {
                    if (!is_file($folder . DIRECTORY_SEPARATOR . $v)) {
                        $this->open_folder($folder . DIRECTORY_SEPARATOR . $v, $folder);
                    } else {
                        $finish_folder = $this->checkFiles($folder . DIRECTORY_SEPARATOR . $v);
                        $way = explode(DIRECTORY_SEPARATOR, realpath($this->primaryFolder));
                        $this->files[$this->to . substr(realpath($finish_folder), strlen(realpath($this->primaryFolder)) - strlen(array_pop($way)))] = $finish_folder;
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Указывает в какую папку упаковывать данные
     * Полезно, когда нужно указать более глубокую папку для упаковки
     * @param $toFolder
     */
    public function in($toFolder)
    {
        if (!is_string($toFolder)) {
            throw new \Exception('Must bee string');
        }
        $this->to = $toFolder;
        return $this;

    }

    /**
     * Добавить в архив файл или папку
     * @param $f
     * @return $this
     * @throws \Exception
     */
    public function add($f)
    {
        if (!is_dir($f) and !is_file($f) and !is_array($f)) {
            throw new \Exception('Bad path: ' . $f);
        }

        if (substr($this->to, -1) != DIRECTORY_SEPARATOR and !empty($this->to)) {
            $this->to = $this->to . DIRECTORY_SEPARATOR;
        }

        if (is_dir($f)) {
            $this->open_folder($f);
        }
        if (is_file($f)) {
            $file = $this->checkFiles($f);
            if (key_exists($this->to . basename($file), $this->files)) {
                throw new \Exception('The file already ' . $this->to . basename($file) . ' exists.');
            }
            $this->files[$this->to . basename($file)] = $file;
        }
        if (is_array($f)) {
            foreach ($f as $k => $v) {
                if (is_file($f)) {
                    $file = $this->checkFiles($f);
                    $this->files[$this->to . basename($file)] = $file;
                }
                if (is_dir($f)) {
                    $this->open_folder($f);
                }
            }
        }


//        echo "<pre>";
//        print_r($this->files);
        $this->to = '';
        return $this;
    }

    /**
     * Создать архив
     * @return bool
     */
    public function end()
    {
        if ($this->archive == null) {
            throw new \Exception('Before use make function');
        }
        array_unique($this->files);
        $zip = new \ZipArchive();
        $zip->open($this->archive, \ZipArchive::CREATE);
        foreach ($this->files as $k => $v) {
            $zip->addFile($v, $k);
        }
        $zip->close();
        return true;
    }

    /**
     * Распаковывет архив в папку назначения
     * @param $source
     * @param $destination
     * @param bool $createFolder
     * @throws \Exception
     */
    public function unzip($source, $destination, $createFolder = false)
    {
        if (!$createFolder and !file_exists($destination)) {
            throw new \Exception('The destination path is missing');
        } else {
            if (!file_exists($destination)) {
                if (!mkdir($destination, 0777, true)) {
                    throw new \Exception('Can not create destination path ');
                };
            }
        }
        foreach ((array)glob($source) as $key => $value) {
            $zip = new \ZipArchive();
            if ($zip->open(str_replace("//", '/', $value)) === true) {
                $zip->extractTo($destination);
                $zip->close();
            }
        }
    }


}

?>
