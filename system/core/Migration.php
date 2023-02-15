<?php
/**
 * Create by e-Mind Studio
 * User: Евгения
 * Date: 30.08.2017
 * Time: 9:16
 */

namespace core;

class Migration extends Model
{
    protected $db;
    public $errorInfo;
    public $table = "migrations";
    public $migrationPath = 'migrations';
    const BASE_MIGRATION_NAME = "m00000_00000_base";

    public function migrateUp($class, $module = '')
    {
        if ($class === self::BASE_MIGRATION_NAME) {
            return true;
        }

        Console::stdout("*** applying " . $class->version . "\n");
        $start     = microtime(true);
        $migration = $this->createMigration($class, $module);
        if ($migration->up() !== false) {
            $this->addMigrationHistory($class, $module);
            $time = microtime(true) - $start;
            Console::stdout("*** applied $class (time: " . sprintf('%.3f', $time) . "s)\n\n");

            return true;
        } else {
            $time = microtime(true) - $start;
            Console::stdout("*** failed to apply $class (time: " . sprintf('%.3f', $time) . "s)\n\n");

            return false;
        }

    }

    public function migrateUpNoConsole($class, $module = '')
    {
        if ($class === self::BASE_MIGRATION_NAME) {
            return true;
        }
        $migration = $this->createMigration($class, $module);
        $migration->up();
        if ($migration->getError() === null) {
            $this->addMigrationHistory($class, $module);
            return true;
        } else {
            $this->errorInfo = $migration->getError();
            return false;
        }
    }

    public function migrateDown($class, $module = '')
    {
        if ($class === self::BASE_MIGRATION_NAME) {
            return true;
        }

        Console::stdout("*** reverting $class\n");
        $start     = microtime(true);
        $migration = $this->createMigration($class, $module);

        if ($migration->down() !== false) {
            $this->removeMigrationHistory($class);
            $time = microtime(true) - $start;
            Console::stdout("*** reverted $class (time: " . sprintf('%.3f', $time) . "s)\n\n");

            return true;
        } else {
            $time = microtime(true) - $start;
            Console::stdout("*** failed to revert $class (time: " . sprintf('%.3f', $time) . "s)\n\n");

            return false;
        }
    }

    public function migrateDownNoConsole($class, $module = '')
    {

        if ($class === self::BASE_MIGRATION_NAME) {
            return true;
        }

        $migration = $this->createMigration($class, $module);
        if (!$migration) {
            $this->removeMigrationHistory($class);
        } else {
            if ($migration->down() !== false) {
                $this->removeMigrationHistory($class);
                return true;
            } else {
                return false;
            }
        }

    }

    public function down()
    {

    }

    public function getMigrationHistory($limit)
    {

        $rows = $this->getAll();

        $history = [];
        foreach ($rows as $key => $row) {
            if ($row->version === self::BASE_MIGRATION_NAME) {
                continue;
            }
            if (preg_match('/m?(\d{6}_?\d{6})(\D.*)?$/is', $row->version, $matches)) {
                $time                  = str_replace('_', '', $matches[1]);
                $row->canonicalVersion = $time;
            } else {
                $row->canonicalVersion = $row->version;
            }
            $row->apply_time = (int)$row->apply_time;
            $history[]       = $row;
        }

        usort($history, function ($a, $b) {
            if ($a->apply_time === $b->apply_time) {
                if (($compareResult = strcasecmp($b->canonicalVersion, $a->canonicalVersion)) !== 0) {
                    return $compareResult;
                }
                return strcasecmp($b->version, $a->version);
            }
            return ($a->apply_time > $b->apply_time) ? -1 : +1;
        });

        $history     = array_slice($history, 0, $limit);
        $new_history = [];
        foreach ($history AS $item) {
            $new_history[$item->version] = ['apply_time' => $item->apply_time, 'module' => $item->module_name];
        }
        $history = $new_history;

        return $history;
    }

    public function getNewMigrations()
    {
        $applied = [];
        foreach ($this->getAll() as $item) {
            $applied[$item->version] = true;
        };
        $migrations = [];
        $handle     = opendir(_SYS_PATH_ . DIRECTORY_SEPARATOR . $this->migrationPath);
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = _SYS_PATH_ . DIRECTORY_SEPARATOR . $this->migrationPath . DIRECTORY_SEPARATOR . $file;
            if (preg_match('/^(m(\d{6}_?\d{6})\D.*?)\.php$/is', $file, $matches) && is_file($path)) {
                $class = $matches[1];
                if (!empty($namespace)) {
                    $class = $namespace . '\\' . $class;
                }
                $time = str_replace('_', '', $matches[2]);
                if (!isset($applied[$class])) {
                    $migrations[$time . '\\' . $class] = ['module' => '', 'class' => $class];
                }
            }
        }
        closedir($handle);
        $handle = _MOD_PATH_ . DIRECTORY_SEPARATOR;
        foreach (glob($handle . '*', GLOB_ONLYDIR | GLOB_MARK) as $dir) {
            $segments     = explode(DIRECTORY_SEPARATOR, $dir);
            $last_element = array_pop($segments);
            if (empty($last_element)) {
                $last_element = array_pop($segments);
            }
            if (file_exists($dir . $this->migrationPath)) {
                $opendir = opendir($dir . $this->migrationPath);
                while (($file = readdir($opendir)) !== false) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    $path = $dir . $this->migrationPath . DIRECTORY_SEPARATOR . $file;
                    if (preg_match('/^(m(\d{6}_?\d{6})\D.*?)\.php$/is', $file, $matches) && is_file($path)) {
                        $class = $matches[1];
                        if (!empty($namespace)) {
                            $class = $namespace . '\\' . $class;
                        }
                        $time = str_replace('_', '', $matches[2]);
                        if (!isset($applied[$class])) {
                            $migrations[$time . '\\' . $class] =
                                ['module' => $last_element, 'class' => $class];
                        }
                    }
                }
                closedir($opendir);
            }
        }
        ksort($migrations);

        return $migrations;
    }

    protected function addMigrationHistory($version, $module = '')
    {
        $this->insert([
                          'version'     => $version,
                          'apply_time'  => time(),
                          'module_name' => $module
                      ]);
    }

    protected function createMigration($class, $module = '')
    {
        $class = trim($class, '\\');
        if (strpos($class, '\\') === false) {
            if (empty($module)) {
                $file = _SYS_PATH_ . DIRECTORY_SEPARATOR . $this->migrationPath . DIRECTORY_SEPARATOR . $class . '.php';
            } else {
                $modPath = _MOD_PATH_;
                if (substr(_MOD_PATH_, -1, 1) != DIRECTORY_SEPARATOR) {
                    $modPath = _MOD_PATH_ . DIRECTORY_SEPARATOR;
                }
                $file = $modPath . $module . DIRECTORY_SEPARATOR . $this->migrationPath . DIRECTORY_SEPARATOR . $class . '.php';
            }
            if (file_exists($file)) {
                require_once($file);
                return new $class();
            } else {
                return false;
            }

        }


    }

    protected function removeMigrationHistory($version)
    {
        $this->clear();
        $this->delete([
                          'version' => $version,
                      ]);
    }

    public function create($name)
    {
        $module  = '';
        $explode = explode('/', $name);
        if (!empty($explode[1])) {
            $module = $explode[0];
            $name   = $explode[1];
        }
        $version = gmdate('ymd_His') . "_" . $name;
        Console::stdout('Create migration ' . $version . PHP_EOL);
        if (empty($module)) {
            if (!file_exists(_SYS_PATH_ . DIRECTORY_SEPARATOR . $this->migrationPath)) {
                mkdir(_SYS_PATH_ . DIRECTORY_SEPARATOR . $this->migrationPath, 0777);
            }
        } else {
            if (!file_exists(_MOD_PATH_ . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $this->migrationPath)) {
                mkdir(_MOD_PATH_ . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $this->migrationPath, 0777);
            }
        }

        $file    = $this->createFullFileName($version, $module);
        $class   = $this->createClassName($version);
        $content =
            <<<END
<?php
use core\Migration;
                  
class {$class} extends Migration
{
     public function up() {
     }
                 
     public function down() {
     }
}
END;
        file_put_contents($file, $content);
    }

    public function createNoConsole($name)
    {
        $module  = '';
        $explode = explode('/', $name);
        if (!empty($explode[1])) {
            $module = $explode[0];
            $name   = $explode[1];
        }
        $version = gmdate('ymd_His') . "_" . $name;
        if (empty($module)) {
            if (!file_exists(_SYS_PATH_ . DIRECTORY_SEPARATOR . $this->migrationPath)) {
                mkdir(_SYS_PATH_ . DIRECTORY_SEPARATOR . $this->migrationPath, 0777);
            }
        } else {
            if (!file_exists(_MOD_PATH_ . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $this->migrationPath)) {
                mkdir(_MOD_PATH_ . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $this->migrationPath, 0777);
            }
        }

        $file    = $this->createFullFileName($version, $module);
        $class   = $this->createClassName($version);
        $content =
            <<<END
<?php
use core\Migration;
                  
class {$class} extends Migration
{
     public function up() {
     }
                 
     public function down() {
     }
}
END;
        file_put_contents($file, $content);
    }

    public function help()
    {
        echo <<<END
Usage:
    php emind migrate/<action>
Actions:
    up [<count>]
    down [<count>]
    create [<moduleName>/][<name>]
 
END;
    }

    private function createClassName($version)
    {
        return 'm' . $version;
    }

    private function createFullFileName($version, $module = '')
    {
        if (empty($module)) {
            return _SYS_PATH_ . DIRECTORY_SEPARATOR . $this->migrationPath . DIRECTORY_SEPARATOR . $this->createFileName($version);
        } else {
            return _MOD_PATH_ . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $this->migrationPath . DIRECTORY_SEPARATOR . $this->createFileName($version);
        }

    }

    private function createFileName($version)
    {
        return 'm' . $version . '.php';
    }

    public function checkEnvironment()
    {
//        die('here1');
        if (!file_exists(_SYS_PATH_ . DIRECTORY_SEPARATOR . $this->migrationPath)) {
            mkdir(_SYS_PATH_ . DIRECTORY_SEPARATOR . $this->migrationPath);
        }
        if (!($this->query("Show tables like '" . $this->table . "'")->fetchAll(\PDO::FETCH_ASSOC))) {
            Console::stdout("Creating migration history table " . $this->table . "...");
            if ($this->query('CREATE TABLE IF NOT EXISTS ' . $this->table . ' (version varchar(180) NOT NULL PRIMARY KEY,
            apply_time integer, module_name varchar(100)) ENGINE=MyISAM DEFAULT CHARSET=utf8;')->execute()
            ) {
                $this->insert(['version' => self::BASE_MIGRATION_NAME, 'apply_time' => time(), 'module_name' => '']);
            };

            Console::stdout("Done.\n");
        }


    }

    public function checkEnvironmentNoConsole()
    {
        if (!file_exists(_SYS_PATH_ . DIRECTORY_SEPARATOR . $this->migrationPath)) {
            mkdir(_SYS_PATH_ . DIRECTORY_SEPARATOR . $this->migrationPath);
        }
        if (!($this->query("Show tables like '" . $this->table . "'")->fetchAll())) {
            if ($this->query('CREATE TABLE IF NOT EXISTS ' . $this->table . ' (version varchar(180) NOT NULL PRIMARY KEY,
            apply_time integer,module_name varchar(100)) ENGINE=MyISAM DEFAULT CHARSET=utf8;')->execute()
            ) {
                $this->insert(['version' => self::BASE_MIGRATION_NAME, 'apply_time' => time(), 'module_name' => '']);
            };
            return true;
        } else {
            return false;
        }
    }

}