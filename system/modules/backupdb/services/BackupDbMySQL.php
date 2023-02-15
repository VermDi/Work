<?
/*
 * https://github.com/ifsnop/mysqldump-php
 */

namespace modules\backupdb\services;

use Exception;
use PDO;
use PDOException;

class BackupDbMySQL
{
    const MAXLINESIZE = 1000000;
    const GZIP = 'Gzip';
    const BZIP2 = 'Bzip2';
    const NONE = 'None';
    const UTF8 = 'utf8';
    const UTF8MB4 = 'utf8mb4';
    public $user;
    public $pass;
    public $dsn;
    public $fileName = 'php://output';

    private $tables = array();
    private $views = array();
    private $triggers = array();
    private $procedures = array();
    private $dbHandler = null;
    private $dbType;
    private $compressManager;
    private $typeAdapter;
    private $dumpSettings = array();
    private $pdoSettings = array();
    private $version;
    private $tableColumnTypes = array();

    private $dbName;
    private $host;
    private $dsnArray = array();

    public function __construct(
        $dsn = '',
        $user = '',
        $pass = '',
        $dumpSettings = array(),
        $pdoSettings = array()
    )
    {
        $dumpSettingsDefault = array(
            'include-tables' => array(),
            'exclude-tables' => array(),
            'compress' => BackupDbMySQL::NONE,
            'no-data' => array(),
            'reset-auto-increment' => false,
            'add-drop-table' => false,
            'single-transaction' => true,
            'lock-tables' => false,// true,
            'add-locks' => false,//true,
            'extended-insert' => true,
            'complete-insert' => false,
            'disable-keys' => true,
            'where' => '',
            'no-create-info' => false,
            'skip-triggers' => false,
            'add-drop-trigger' => true,
            'routines' => false,
            'hex-blob' => true,
            'databases' => false,
            'add-drop-database' => false,
            'skip-tz-utc' => false,
            'no-autocommit' => true,
            'default-character-set' => BackupDbMySQL::UTF8,
            'skip-comments' => false,
            'skip-dump-date' => false,
            'init_commands' => array(),
            'net_buffer_length' => self::MAXLINESIZE,
            'disable-foreign-keys-check' => true
        );

        $pdoSettingsDefault = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false
        );

        $this->user = $user;
        $this->pass = $pass;
        $this->parseDsn($dsn);
        $this->pdoSettings = self::array_replace_recursive($pdoSettingsDefault, $pdoSettings);
        $this->dumpSettings = self::array_replace_recursive($dumpSettingsDefault, $dumpSettings);

        $this->dumpSettings['init_commands'][] = "SET NAMES " . $this->dumpSettings['default-character-set'];

        if (false === $this->dumpSettings['skip-tz-utc']) {
            $this->dumpSettings['init_commands'][] = "SET TIME_ZONE='+00:00'";
        }

        $diff = array_diff(array_keys($this->dumpSettings), array_keys($dumpSettingsDefault));
        if (count($diff) > 0) {
            throw new Exception("Unexpected value in dumpSettings: (" . implode(",", $diff) . ")");
        }

        if (!is_array($this->dumpSettings['include-tables']) ||
            !is_array($this->dumpSettings['exclude-tables'])
        ) {
            throw new Exception("Include-tables and exclude-tables should be arrays");
        }
        $this->dumpSettings['include-views'] = $this->dumpSettings['include-tables'];
        $this->compressManager = CompressManagerFactory::create($this->dumpSettings['compress']);
    }

    private function parseDsn($dsn)
    {
        if (empty($dsn) || (false === ($pos = strpos($dsn, ":")))) {
            throw new Exception("Empty DSN string");
        }

        $this->dsn = $dsn;
        $this->dbType = strtolower(substr($dsn, 0, $pos));

        if (empty($this->dbType)) {
            throw new Exception("Missing database type from DSN string");
        }

        $dsn = substr($dsn, $pos + 1);

        foreach (explode(";", $dsn) as $kvp) {
            $kvpArr = explode("=", $kvp);
            $this->dsnArray[strtolower($kvpArr[0])] = $kvpArr[1];
        }

        if (empty($this->dsnArray['host']) &&
            empty($this->dsnArray['unix_socket'])
        ) {
            throw new Exception("Missing host from DSN string");
        }
        $this->host = (!empty($this->dsnArray['host'])) ?
            $this->dsnArray['host'] :
            $this->dsnArray['unix_socket'];

        if (empty($this->dsnArray['dbname'])) {
            throw new Exception("Missing database name from DSN string");
        }

        $this->dbName = $this->dsnArray['dbname'];

        return true;
    }

    public static function array_replace_recursive($array1, $array2)
    {
        if (function_exists('array_replace_recursive')) {
            return array_replace_recursive($array1, $array2);
        }

        foreach ($array2 as $key => $value) {
            if (is_array($value)) {
                $array1[$key] = self::array_replace_recursive($array1[$key], $value);
            } else {
                $array1[$key] = $value;
            }
        }
        return $array1;
    }

    public function __destruct()
    {
        $this->dbHandler = null;
    }

    public function restore($filename = '')
    {
        $res = 0;
        if (!empty($filename)) {
            $this->fileName = $filename;
        }

        $this->connect();
        //echo 'filename: ' . $filename . '<br>';

        try {
            $qls = 'SHOW TABLES FROM ' . $this->dbName;
            $sth = $this->dbHandler->prepare($qls);
            $sth->execute();
            $rslt = $sth->fetchAll();
            foreach ($rslt as $q => $m) {
                $table = $m['Tables_in_' . $this->dbName];
                echo 'table: ' . $table . '<br>';
                $qls1 = 'DROP TABLE ' . $table;
                $sth1 = $this->dbHandler->prepare($qls1);
                $sth1->execute();
            }

            $qls = file_get_contents($filename);
            $sth = $this->dbHandler->prepare($qls);
            $sth->execute();
            $res = 1;
        } catch (PDOException $e) {
            echo 'error: <pre>' . print_r($e, true) . '</pre>';
        }
        return $res;
    }

    private function connect()
    {
        try {
            switch ($this->dbType) {
                case 'sqlite':
                    $this->dbHandler = @new PDO("sqlite:" . $this->dbName, null, null, $this->pdoSettings);
                    break;
                case 'mysql':
                case 'pgsql':
                case 'dblib':
                    $this->dbHandler = @new PDO(
                        $this->dsn,
                        $this->user,
                        $this->pass,
                        $this->pdoSettings
                    );
                    foreach ($this->dumpSettings['init_commands'] as $stmt) {
                        $this->dbHandler->exec($stmt);
                    }
                    $this->version = $this->dbHandler->getAttribute(PDO::ATTR_SERVER_VERSION);
                    break;
                default:
                    throw new Exception("Unsupported database type (" . $this->dbType . ")");
            }
        } catch (PDOException $e) {
            throw new Exception(
                "Connection to " . $this->dbType . " failed with message: " .
                $e->getMessage()
            );
        }

        if (is_null($this->dbHandler)) {
            throw new Exception("Connection to " . $this->dbType . "failed");
        }

        $this->dbHandler->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
        $this->typeAdapter = TypeAdapterFactory::create($this->dbType, $this->dbHandler);
    }

    public function start($filename = '')
    {
        if (!empty($filename)) {
            $this->fileName = $filename;
        }

        $this->connect();

        $this->compressManager->open($this->fileName);

        $this->compressManager->write($this->getDumpFileHeader());

        $this->compressManager->write(
            $this->typeAdapter->backup_parameters($this->dumpSettings)
        );

        if ($this->dumpSettings['databases']) {
            $this->compressManager->write(
                $this->typeAdapter->getDatabaseHeader($this->dbName)
            );
            if ($this->dumpSettings['add-drop-database']) {
                $this->compressManager->write(
                    $this->typeAdapter->add_drop_database($this->dbName)
                );
            }
        }

        $this->getDatabaseStructure();

        if ($this->dumpSettings['databases']) {
            $this->compressManager->write(
                $this->typeAdapter->databases($this->dbName)
            );
        }

        if (0 < count($this->dumpSettings['include-tables'])) {
            $name = implode(",", $this->dumpSettings['include-tables']);
            throw new Exception("Table (" . $name . ") not found in database");
        }

        $this->exportTables();
        $this->exportViews();
        $this->exportTriggers();
        $this->exportProcedures();

        $this->compressManager->write(
            $this->typeAdapter->restore_parameters($this->dumpSettings)
        );
        $this->compressManager->write($this->getDumpFileFooter());
        $this->compressManager->close();
    }

    private function getDumpFileHeader()
    {
        $header = '';
        if (!$this->dumpSettings['skip-comments']) {
            $header = "-- BackupDbMySQL mindCMS" . PHP_EOL .
                "--" . PHP_EOL .
                "-- Host: {$this->host}\tDatabase: {$this->dbName}" . PHP_EOL .
                "-- ------------------------------------------------------" . PHP_EOL;

            if (!empty($this->version)) {
                $header .= "-- Server version \t" . $this->version . PHP_EOL;
            }

            if (!$this->dumpSettings['skip-dump-date']) {
                $header .= "-- Date: " . date('r') . PHP_EOL . PHP_EOL;
            }
        }
        return $header;
    }

    private function getDatabaseStructure()
    {
        if (empty($this->dumpSettings['include-tables'])) {
            foreach ($this->dbHandler->query($this->typeAdapter->show_tables($this->dbName)) as $row) {
                array_push($this->tables, current($row));
            }
        } else {
            foreach ($this->dbHandler->query($this->typeAdapter->show_tables($this->dbName)) as $row) {
                if (in_array(current($row), $this->dumpSettings['include-tables'], true)) {
                    array_push($this->tables, current($row));
                    $elem = array_search(
                        current($row),
                        $this->dumpSettings['include-tables']
                    );
                    unset($this->dumpSettings['include-tables'][$elem]);
                }
            }
        }

        if (empty($this->dumpSettings['include-views'])) {
            foreach ($this->dbHandler->query($this->typeAdapter->show_views($this->dbName)) as $row) {
                array_push($this->views, current($row));
            }
        } else {
            foreach ($this->dbHandler->query($this->typeAdapter->show_views($this->dbName)) as $row) {
                if (in_array(current($row), $this->dumpSettings['include-views'], true)) {
                    array_push($this->views, current($row));
                    $elem = array_search(
                        current($row),
                        $this->dumpSettings['include-views']
                    );
                    unset($this->dumpSettings['include-views'][$elem]);
                }
            }
        }

        if (false === $this->dumpSettings['skip-triggers']) {
            foreach ($this->dbHandler->query($this->typeAdapter->show_triggers($this->dbName)) as $row) {
                array_push($this->triggers, $row['Trigger']);
            }
        }

        if ($this->dumpSettings['routines']) {
            foreach ($this->dbHandler->query($this->typeAdapter->show_procedures($this->dbName)) as $row) {
                array_push($this->procedures, $row['procedure_name']);
            }
        }
    }

    private function exportTables()
    {
        foreach ($this->tables as $table) {
            if ($this->matches($table, $this->dumpSettings['exclude-tables'])) {
                continue;
            }
            $this->getTableStructure($table);
            if (false === $this->dumpSettings['no-data']) { // don't break compatibility with old trigger
                $this->listValues($table);
            } else if (true === $this->dumpSettings['no-data']
                || $this->matches($table, $this->dumpSettings['no-data'])
            ) {
                continue;
            } else {
                $this->listValues($table);
            }
        }
    }

    private function matches($table, $arr)
    {
        $match = false;

        foreach ($arr as $pattern) {
            if ('/' != $pattern[0]) {
                continue;
            }
            if (1 == preg_match($pattern, $table)) {
                $match = true;
            }
        }
        return in_array($table, $arr) || $match;
    }

    private function getTableStructure($tableName)
    {
        if (!$this->dumpSettings['no-create-info']) {
            $ret = '';
            if (!$this->dumpSettings['skip-comments']) {
                $ret = "--" . PHP_EOL .
                    "-- Table structure for table `$tableName`" . PHP_EOL .
                    "--" . PHP_EOL . PHP_EOL;
            }
            $stmt = $this->typeAdapter->show_create_table($tableName);
            foreach ($this->dbHandler->query($stmt) as $r) {
                $this->compressManager->write($ret);
                if ($this->dumpSettings['add-drop-table']) {
                    $this->compressManager->write(
                        $this->typeAdapter->drop_table($tableName)
                    );
                }
                $this->compressManager->write(
                    $this->typeAdapter->create_table($r, $this->dumpSettings)
                );
                break;
            }
        }
        $this->tableColumnTypes[$tableName] = $this->getTableColumnTypes($tableName);
        return;
    }

    private function getTableColumnTypes($tableName)
    {
        $columnTypes = array();
        $columns = $this->dbHandler->query(
            $this->typeAdapter->show_columns($tableName)
        );
        $columns->setFetchMode(PDO::FETCH_ASSOC);

        foreach ($columns as $key => $col) {
            $types = $this->typeAdapter->parseColumnType($col);
            $columnTypes[$col['Field']] = array(
                'is_numeric' => $types['is_numeric'],
                'is_blob' => $types['is_blob'],
                'type' => $types['type'],
                'type_sql' => $col['Type']
            );
        }
        return $columnTypes;
    }

    private function listValues($tableName)
    {
        $this->prepareListValues($tableName);

        $onlyOnce = true;
        $lineSize = 0;

        $colStmt = $this->getColumnStmt($tableName);
        $stmt = "SELECT $colStmt FROM `$tableName`";

        if ($this->dumpSettings['where']) {
            $stmt .= " WHERE {$this->dumpSettings['where']}";
        }
        $resultSet = $this->dbHandler->query($stmt);
        $resultSet->setFetchMode(PDO::FETCH_ASSOC);

        foreach ($resultSet as $row) {
            $vals = $this->escape($tableName, $row);
            if ($onlyOnce || !$this->dumpSettings['extended-insert']) {

                if ($this->dumpSettings['complete-insert']) {
                    $lineSize += $this->compressManager->write(
                        "INSERT INTO `$tableName` (`" .
                        implode("`, `", array_keys($this->tableColumnTypes[$tableName])) .
                        "`) VALUES (" . implode(",", $vals) . ")"
                    );
                } else {
                    $lineSize += $this->compressManager->write(
                        "INSERT INTO `$tableName` VALUES (" . implode(",", $vals) . ")"
                    );
                }
                $onlyOnce = false;
            } else {
                $lineSize += $this->compressManager->write(",(" . implode(",", $vals) . ")");
            }
            if (($lineSize > $this->dumpSettings['net_buffer_length']) ||
                !$this->dumpSettings['extended-insert']
            ) {
                $onlyOnce = true;
                $lineSize = $this->compressManager->write(";" . PHP_EOL);
            }
        }
        $resultSet->closeCursor();

        if (!$onlyOnce) {
            $this->compressManager->write(";" . PHP_EOL);
        }

        $this->endListValues($tableName);
    }

    function prepareListValues($tableName)
    {
        if (!$this->dumpSettings['skip-comments']) {
            $this->compressManager->write(
                "--" . PHP_EOL .
                "-- Dumping data for table `$tableName`" . PHP_EOL .
                "--" . PHP_EOL . PHP_EOL
            );
        }

        if ($this->dumpSettings['single-transaction']) {
            $this->dbHandler->exec($this->typeAdapter->setup_transaction());
            $this->dbHandler->exec($this->typeAdapter->start_transaction());
        }

        if ($this->dumpSettings['lock-tables']) {
            $this->typeAdapter->lock_table($tableName);
        }

        if ($this->dumpSettings['add-locks']) {
            $this->compressManager->write(
                $this->typeAdapter->start_add_lock_table($tableName)
            );
        }

        if ($this->dumpSettings['disable-keys']) {
            $this->compressManager->write(
                $this->typeAdapter->start_add_disable_keys($tableName)
            );
        }

        if ($this->dumpSettings['no-autocommit']) {
            $this->compressManager->write(
                $this->typeAdapter->start_disable_autocommit()
            );
        }

        return;
    }

    function getColumnStmt($tableName)
    {
        $colStmt = array();
        foreach ($this->tableColumnTypes[$tableName] as $colName => $colType) {
            if ($colType['type'] == 'bit' && $this->dumpSettings['hex-blob']) {
                $colStmt[] = "LPAD(HEX(`${colName}`),2,'0') AS `${colName}`";
            } else if ($colType['is_blob'] && $this->dumpSettings['hex-blob']) {
                $colStmt[] = "HEX(`${colName}`) AS `${colName}`";
            } else {
                $colStmt[] = "`${colName}`";
            }
        }
        $colStmt = implode($colStmt, ",");

        return $colStmt;
    }

    private function escape($tableName, $row)
    {
        $ret = array();
        $columnTypes = $this->tableColumnTypes[$tableName];
        foreach ($row as $colName => $colValue) {
            if (is_null($colValue)) {
                $ret[] = "NULL";
            } elseif ($this->dumpSettings['hex-blob'] && $columnTypes[$colName]['is_blob']) {
                if ($columnTypes[$colName]['type'] == 'bit' || !empty($colValue)) {
                    $ret[] = "0x${colValue}";
                } else {
                    $ret[] = "''";
                }
            } elseif ($columnTypes[$colName]['is_numeric']) {
                $ret[] = $colValue;
            } else {
                $ret[] = $this->dbHandler->quote($colValue);
            }
        }
        return $ret;
    }

    function endListValues($tableName)
    {
        if ($this->dumpSettings['disable-keys']) {
            $this->compressManager->write(
                $this->typeAdapter->end_add_disable_keys($tableName)
            );
        }

        if ($this->dumpSettings['add-locks']) {
            $this->compressManager->write(
                $this->typeAdapter->end_add_lock_table($tableName)
            );
        }

        if ($this->dumpSettings['single-transaction']) {
            $this->dbHandler->exec($this->typeAdapter->commit_transaction());
        }

        if ($this->dumpSettings['lock-tables']) {
            $this->typeAdapter->unlock_table($tableName);
        }

        if ($this->dumpSettings['no-autocommit']) {
            $this->compressManager->write(
                $this->typeAdapter->end_disable_autocommit()
            );
        }

        $this->compressManager->write(PHP_EOL);

        return;
    }

    private function exportViews()
    {
        if (false === $this->dumpSettings['no-create-info']) {
            foreach ($this->views as $view) {
                if ($this->matches($view, $this->dumpSettings['exclude-tables'])) {
                    continue;
                }
                $this->tableColumnTypes[$view] = $this->getTableColumnTypes($view);
                $this->getViewStructureTable($view);
            }
            foreach ($this->views as $view) {
                if ($this->matches($view, $this->dumpSettings['exclude-tables'])) {
                    continue;
                }
                $this->getViewStructureView($view);
            }
        }
    }

    private function getViewStructureTable($viewName)
    {
        if (!$this->dumpSettings['skip-comments']) {
            $ret = "--" . PHP_EOL .
                "-- Stand-In structure for view `${viewName}`" . PHP_EOL .
                "--" . PHP_EOL . PHP_EOL;
            $this->compressManager->write($ret);
        }
        $stmt = $this->typeAdapter->show_create_view($viewName);

        foreach ($this->dbHandler->query($stmt) as $r) {
            if ($this->dumpSettings['add-drop-table']) {
                $this->compressManager->write(
                    $this->typeAdapter->drop_view($viewName)
                );
            }

            $this->compressManager->write(
                $this->createStandInTable($viewName)
            );
            break;
        }
    }

    function createStandInTable($viewName)
    {
        $ret = array();
        foreach ($this->tableColumnTypes[$viewName] as $k => $v) {
            $ret[] = "`${k}` ${v['type_sql']}";
        }
        $ret = implode(PHP_EOL . ",", $ret);

        $ret = "CREATE TABLE IF NOT EXISTS `$viewName` (" .
            PHP_EOL . $ret . PHP_EOL . ");" . PHP_EOL;

        return $ret;
    }

    private function getViewStructureView($viewName)
    {
        if (!$this->dumpSettings['skip-comments']) {
            $ret = "--" . PHP_EOL .
                "-- View structure for view `${viewName}`" . PHP_EOL .
                "--" . PHP_EOL . PHP_EOL;
            $this->compressManager->write($ret);
        }
        $stmt = $this->typeAdapter->show_create_view($viewName);

        foreach ($this->dbHandler->query($stmt) as $r) {
            $this->compressManager->write(
                $this->typeAdapter->drop_view($viewName)
            );
            $this->compressManager->write(
                $this->typeAdapter->create_view($r)
            );
            break;
        }
    }

    private function exportTriggers()
    {
        foreach ($this->triggers as $trigger) {
            $this->getTriggerStructure($trigger);
        }
    }

    private function getTriggerStructure($triggerName)
    {
        $stmt = $this->typeAdapter->show_create_trigger($triggerName);
        foreach ($this->dbHandler->query($stmt) as $r) {
            if ($this->dumpSettings['add-drop-trigger']) {
                $this->compressManager->write(
                    $this->typeAdapter->add_drop_trigger($triggerName)
                );
            }
            $this->compressManager->write(
                $this->typeAdapter->create_trigger($r)
            );
            return;
        }
    }

    private function exportProcedures()
    {
        foreach ($this->procedures as $procedure) {
            $this->getProcedureStructure($procedure);
        }
    }

    private function getProcedureStructure($procedureName)
    {
        if (!$this->dumpSettings['skip-comments']) {
            $ret = "--" . PHP_EOL .
                "-- Dumping routines for database '" . $this->dbName . "'" . PHP_EOL .
                "--" . PHP_EOL . PHP_EOL;
            $this->compressManager->write($ret);
        }
        $stmt = $this->typeAdapter->show_create_procedure($procedureName);
        foreach ($this->dbHandler->query($stmt) as $r) {
            $this->compressManager->write(
                $this->typeAdapter->create_procedure($r, $this->dumpSettings)
            );
            return;
        }
    }

    private function getDumpFileFooter()
    {
        $footer = '';
        if (!$this->dumpSettings['skip-comments']) {
            $footer .= '-- Dump completed';
            if (!$this->dumpSettings['skip-dump-date']) {
                $footer .= ' on: ' . date('r');
            }
            $footer .= PHP_EOL;
        }
        return $footer;
    }
}

abstract class CompressMethod
{
    public static $enums = array(
        "None",
        "Gzip",
        "Bzip2"
    );

    public static function isValid($c)
    {
        return in_array($c, self::$enums);
    }
}

abstract class CompressManagerFactory
{
    public static function create($c)
    {
        $c = ucfirst(strtolower($c));
        if (!CompressMethod::isValid($c)) {
            throw new Exception("Compression method ($c) is not defined yet");
        }

        $method = __NAMESPACE__ . "\\" . "Compress" . $c;

        return new $method;
    }
}

class CompressBzip2 extends CompressManagerFactory
{
    private $fileHandler = null;

    public function __construct()
    {
        if (!function_exists("bzopen")) {
            throw new Exception("Compression is enabled, but bzip2 lib is not installed or configured properly");
        }
    }

    public function open($filename)
    {
        $this->fileHandler = bzopen($filename, "w");
        if (false === $this->fileHandler) {
            throw new Exception("Output file is not writable");
        }

        return true;
    }

    public function write($str)
    {
        if (false === ($bytesWritten = bzwrite($this->fileHandler, $str))) {
            throw new Exception("Writting to file failed! Probably, there is no more free space left?");
        }
        return $bytesWritten;
    }

    public function close()
    {
        return bzclose($this->fileHandler);
    }
}

class CompressGzip extends CompressManagerFactory
{
    private $fileHandler = null;

    public function __construct()
    {
        if (!function_exists("gzopen")) {
            throw new Exception("Compression is enabled, but gzip lib is not installed or configured properly");
        }
    }

    public function open($filename)
    {
        $this->fileHandler = gzopen($filename, "wb");
        if (false === $this->fileHandler) {
            throw new Exception("Output file is not writable");
        }

        return true;
    }

    public function write($str)
    {
        if (false === ($bytesWritten = gzwrite($this->fileHandler, $str))) {
            throw new Exception("Writting to file failed! Probably, there is no more free space left?");
        }
        return $bytesWritten;
    }

    public function close()
    {
        return gzclose($this->fileHandler);
    }
}

class CompressNone extends CompressManagerFactory
{
    private $fileHandler = null;

    public function open($filename)
    {
        if (!file_exists($filename)) {
            $dir = dirname($filename);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }
        $this->fileHandler = fopen($filename, "wb");
        if (false === $this->fileHandler) {
            throw new Exception("Output file is not writable");
        }

        return true;
    }

    public function write($str)
    {
        if (false === ($bytesWritten = fwrite($this->fileHandler, $str))) {
            throw new Exception("Writting to file failed! Probably, there is no more free space left?");
        }
        return $bytesWritten;
    }

    public function close()
    {
        return fclose($this->fileHandler);
    }
}

abstract class TypeAdapter
{
    public static $enums = array(
        "Sqlite",
        "Mysql"
    );

    public static function isValid($c)
    {
        return in_array($c, self::$enums);
    }
}

abstract class TypeAdapterFactory
{
    public static function create($c, $dbHandler = null)
    {
        $c = ucfirst(strtolower($c));
        if (!TypeAdapter::isValid($c)) {
            throw new Exception("Database type support for ($c) not yet available");
        }
        $method = __NAMESPACE__ . "\\" . "TypeAdapter" . $c;
        return new $method($dbHandler);
    }

    public function databases()
    {
        return "";
    }

    public function show_create_table($tableName)
    {
        return "SELECT tbl_name as 'Table', sql as 'Create Table' " .
            "FROM sqlite_master " .
            "WHERE type='table' AND tbl_name='$tableName'";
    }

    public function create_table($row, $dumpSettings)
    {
        return "";
    }

    public function show_create_view($viewName)
    {
        return "SELECT tbl_name as 'View', sql as 'Create View' " .
            "FROM sqlite_master " .
            "WHERE type='view' AND tbl_name='$viewName'";
    }

    public function create_view($row)
    {
        return "";
    }

    public function show_create_trigger($triggerName)
    {
        return "";
    }

    public function create_trigger($triggerName)
    {
        return "";
    }

    public function create_procedure($procedureName, $dumpSettings)
    {
        return "";
    }

    public function show_tables()
    {
        return "SELECT tbl_name FROM sqlite_master WHERE type='table'";
    }

    public function show_views()
    {
        return "SELECT tbl_name FROM sqlite_master WHERE type='view'";
    }

    public function show_triggers()
    {
        return "SELECT name FROM sqlite_master WHERE type='trigger'";
    }

    public function show_columns()
    {
        if (func_num_args() != 1) {
            return "";
        }

        $args = func_get_args();

        return "pragma table_info(${args[0]})";
    }

    public function show_procedures()
    {
        return "";
    }

    public function setup_transaction()
    {
        return "";
    }

    public function start_transaction()
    {
        return "BEGIN EXCLUSIVE";
    }

    public function commit_transaction()
    {
        return "COMMIT";
    }

    public function lock_table()
    {
        return "";
    }

    public function unlock_table()
    {
        return "";
    }

    public function start_add_lock_table()
    {
        return PHP_EOL;
    }

    public function end_add_lock_table()
    {
        return PHP_EOL;
    }

    public function start_add_disable_keys()
    {
        return PHP_EOL;
    }

    public function end_add_disable_keys()
    {
        return PHP_EOL;
    }

    public function start_disable_foreign_keys_check()
    {
        return PHP_EOL;
    }

    public function end_disable_foreign_keys_check()
    {
        return PHP_EOL;
    }

    public function add_drop_database()
    {
        return PHP_EOL;
    }

    public function add_drop_trigger()
    {
        return PHP_EOL;
    }

    public function drop_table()
    {
        return PHP_EOL;
    }

    public function drop_view()
    {
        return PHP_EOL;
    }

    public function parseColumnType($colType)
    {
        return array();
    }

    public function backup_parameters()
    {
        return PHP_EOL;
    }

    public function restore_parameters()
    {
        return PHP_EOL;
    }
}

class TypeAdapterPgsql extends TypeAdapterFactory
{
}

class TypeAdapterDblib extends TypeAdapterFactory
{
}

class TypeAdapterSqlite extends TypeAdapterFactory
{
}

class TypeAdapterMysql extends TypeAdapterFactory
{

    public $mysqlTypes = array(
        'numerical' => array(
            'bit',
            'tinyint',
            'smallint',
            'mediumint',
            'int',
            'integer',
            'bigint',
            'real',
            'double',
            'float',
            'decimal',
            'numeric'
        ),
        'blob' => array(
            'tinyblob',
            'blob',
            'mediumblob',
            'longblob',
            'binary',
            'varbinary',
            'bit',
            'geometry',
            'point',
            'linestring',
            'polygon',
            'multipoint',
            'multilinestring',
            'multipolygon',
            'geometrycollection',
        )
    );
    private $dbHandler = null;

    public function __construct($dbHandler)
    {
        $this->dbHandler = $dbHandler;
    }

    public function databases()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        $databaseName = $args[0];

        $resultSet = $this->dbHandler->query("SHOW VARIABLES LIKE 'character_set_database';");
        $characterSet = $resultSet->fetchColumn(1);
        $resultSet->closeCursor();

        $resultSet = $this->dbHandler->query("SHOW VARIABLES LIKE 'collation_database';");
        $collationDb = $resultSet->fetchColumn(1);
        $resultSet->closeCursor();
        $ret = "";

        $ret .= "CREATE DATABASE /*!32312 IF NOT EXISTS*/ `${databaseName}`" .
            " /*!40100 DEFAULT CHARACTER SET ${characterSet} " .
            " COLLATE ${collationDb} */;" . PHP_EOL . PHP_EOL .
            "USE `${databaseName}`;" . PHP_EOL . PHP_EOL;

        return $ret;
    }

    private function check_parameters($num_args, $expected_num_args, $method_name)
    {
        if ($num_args != $expected_num_args) {
            throw new Exception("Unexpected parameter passed to $method_name");
        }
        return;
    }

    public function show_create_table($tableName)
    {
        return "SHOW CREATE TABLE `$tableName`";
    }

    public function show_create_view($viewName)
    {
        return "SHOW CREATE VIEW `$viewName`";
    }

    public function show_create_trigger($triggerName)
    {
        return "SHOW CREATE TRIGGER `$triggerName`";
    }

    public function show_create_procedure($procedureName)
    {
        return "SHOW CREATE PROCEDURE `$procedureName`";
    }

    public function create_table($row, $dumpSettings)
    {
        if (!isset($row['Create Table'])) {
            throw new Exception("Error getting table code, unknown output");
        }

        $createTable = $row['Create Table'];
        if ($dumpSettings['reset-auto-increment']) {
            $match = "/AUTO_INCREMENT=[0-9]+/s";
            $replace = "";
            $createTable = preg_replace($match, $replace, $createTable);
        }

        $ret = "/*!40101 SET @saved_cs_client     = @@character_set_client */;" . PHP_EOL .
            "/*!40101 SET character_set_client = " . $dumpSettings['default-character-set'] . " */;" . PHP_EOL .
            $createTable . ";" . PHP_EOL .
            "/*!40101 SET character_set_client = @saved_cs_client */;" . PHP_EOL .
            PHP_EOL;
        return $ret;
    }

    public function create_view($row)
    {
        $ret = "";
        if (!isset($row['Create View'])) {
            throw new Exception("Error getting view structure, unknown output");
        }

        $triggerStmt = $row['Create View'];

        $triggerStmtReplaced1 = str_replace(
            "CREATE ALGORITHM",
            "/*!50001 CREATE ALGORITHM",
            $triggerStmt
        );
        $triggerStmtReplaced2 = str_replace(
            " DEFINER=",
            " */" . PHP_EOL . "/*!50013 DEFINER=",
            $triggerStmtReplaced1
        );
        $triggerStmtReplaced3 = str_replace(
            " VIEW ",
            " */" . PHP_EOL . "/*!50001 VIEW ",
            $triggerStmtReplaced2
        );
        if (false === $triggerStmtReplaced1 ||
            false === $triggerStmtReplaced2 ||
            false === $triggerStmtReplaced3
        ) {
            $triggerStmtReplaced = $triggerStmt;
        } else {
            $triggerStmtReplaced = $triggerStmtReplaced3 . " */;";
        }

        $ret .= $triggerStmtReplaced . PHP_EOL . PHP_EOL;
        return $ret;
    }

    public function create_trigger($row)
    {
        $ret = "";
        if (!isset($row['SQL Original Statement'])) {
            throw new Exception("Error getting trigger code, unknown output");
        }

        $triggerStmt = $row['SQL Original Statement'];
        $triggerStmtReplaced = str_replace(
            "CREATE DEFINER",
            "/*!50003 CREATE*/ /*!50017 DEFINER",
            $triggerStmt
        );
        $triggerStmtReplaced = str_replace(
            " TRIGGER",
            "*/ /*!50003 TRIGGER",
            $triggerStmtReplaced
        );
        if (false === $triggerStmtReplaced) {
            $triggerStmtReplaced = $triggerStmt;
        }

        $ret .= "DELIMITER ;;" . PHP_EOL .
            $triggerStmtReplaced . "*/;;" . PHP_EOL .
            "DELIMITER ;" . PHP_EOL . PHP_EOL;
        return $ret;
    }

    public function create_procedure($row, $dumpSettings)
    {
        $ret = "";
        if (!isset($row['Create Procedure'])) {
            throw new Exception("Error getting procedure code, unknown output. " .
                "Please check 'https://bugs.mysql.com/bug.php?id=14564'");
        }
        $procedureStmt = $row['Create Procedure'];

        $ret .= "/*!50003 DROP PROCEDURE IF EXISTS `" .
            $row['Procedure'] . "` */;" . PHP_EOL .
            "/*!40101 SET @saved_cs_client     = @@character_set_client */;" . PHP_EOL .
            "/*!40101 SET character_set_client = " . $dumpSettings['default-character-set'] . " */;" . PHP_EOL .
            "DELIMITER ;;" . PHP_EOL .
            $procedureStmt . " ;;" . PHP_EOL .
            "DELIMITER ;" . PHP_EOL .
            "/*!40101 SET character_set_client = @saved_cs_client */;" . PHP_EOL . PHP_EOL;

        return $ret;
    }

    public function show_tables()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "SELECT TABLE_NAME AS tbl_name " .
            "FROM INFORMATION_SCHEMA.TABLES " .
            "WHERE TABLE_TYPE='BASE TABLE' AND TABLE_SCHEMA='${args[0]}'";
    }

    public function show_views()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "SELECT TABLE_NAME AS tbl_name " .
            "FROM INFORMATION_SCHEMA.TABLES " .
            "WHERE TABLE_TYPE='VIEW' AND TABLE_SCHEMA='${args[0]}'";
    }

    public function show_triggers()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "SHOW TRIGGERS FROM `${args[0]}`;";
    }

    public function show_columns()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "SHOW COLUMNS FROM `${args[0]}`;";
    }

    public function show_procedures()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "SELECT SPECIFIC_NAME AS procedure_name " .
            "FROM INFORMATION_SCHEMA.ROUTINES " .
            "WHERE ROUTINE_TYPE='PROCEDURE' AND ROUTINE_SCHEMA='${args[0]}'";
    }

    public function setup_transaction()
    {
        return "SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ";
    }

    public function start_transaction()
    {
        return "START TRANSACTION";
    }

    public function commit_transaction()
    {
        return "COMMIT";
    }

    public function lock_table()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return $this->dbHandler->exec("LOCK TABLES `${args[0]}` READ LOCAL");

    }

    public function unlock_table()
    {
        return $this->dbHandler->exec("UNLOCK TABLES");
    }

    public function start_add_lock_table()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "LOCK TABLES `${args[0]}` WRITE;" . PHP_EOL;
    }

    public function end_add_lock_table()
    {
        return "UNLOCK TABLES;" . PHP_EOL;
    }

    public function start_add_disable_keys()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "/*!40000 ALTER TABLE `${args[0]}` DISABLE KEYS */;" .
            PHP_EOL;
    }

    public function end_add_disable_keys()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "/*!40000 ALTER TABLE `${args[0]}` ENABLE KEYS */;" .
            PHP_EOL;
    }

    public function start_disable_autocommit()
    {
        return "SET autocommit=0;" . PHP_EOL;
    }

    public function end_disable_autocommit()
    {
        return "COMMIT;" . PHP_EOL;
    }

    public function add_drop_database()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "/*!40000 DROP DATABASE IF EXISTS `${args[0]}`*/;" .
            PHP_EOL . PHP_EOL;
    }

    public function add_drop_trigger()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "DROP TRIGGER IF EXISTS `${args[0]}`;" . PHP_EOL;
    }

    public function drop_table()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "DROP TABLE IF EXISTS `${args[0]}`;" . PHP_EOL;
    }

    public function drop_view()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "DROP TABLE IF EXISTS `${args[0]}`;" . PHP_EOL .
            "/*!50001 DROP VIEW IF EXISTS `${args[0]}`*/;" . PHP_EOL;
    }

    public function getDatabaseHeader()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "--" . PHP_EOL .
            "-- Current Database: `${args[0]}`" . PHP_EOL .
            "--" . PHP_EOL . PHP_EOL;
    }

    public function parseColumnType($colType)
    {
        $colInfo = array();
        $colParts = explode(" ", $colType['Type']);

        if ($fparen = strpos($colParts[0], "(")) {
            $colInfo['type'] = substr($colParts[0], 0, $fparen);
            $colInfo['length'] = str_replace(")", "", substr($colParts[0], $fparen + 1));
            $colInfo['attributes'] = isset($colParts[1]) ? $colParts[1] : NULL;
        } else {
            $colInfo['type'] = $colParts[0];
        }
        $colInfo['is_numeric'] = in_array($colInfo['type'], $this->mysqlTypes['numerical']);
        $colInfo['is_blob'] = in_array($colInfo['type'], $this->mysqlTypes['blob']);

        return $colInfo;
    }

    public function backup_parameters()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        $dumpSettings = $args[0];
        $ret = "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;" . PHP_EOL .
            "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;" . PHP_EOL .
            "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;" . PHP_EOL .
            "/*!40101 SET NAMES " . $dumpSettings['default-character-set'] . " */;" . PHP_EOL;

        if (false === $dumpSettings['skip-tz-utc']) {
            $ret .= "/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;" . PHP_EOL .
                "/*!40103 SET TIME_ZONE='+00:00' */;" . PHP_EOL;
        }

        $ret .= "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;" . PHP_EOL .
            "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;" . PHP_EOL .
            "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;" . PHP_EOL .
            "/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;" . PHP_EOL . PHP_EOL;

        return $ret;
    }

    public function restore_parameters()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        $dumpSettings = $args[0];
        $ret = "";

        if (false === $dumpSettings['skip-tz-utc']) {
            $ret .= "/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;" . PHP_EOL;
        }

        $ret .= "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;" . PHP_EOL .
            "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;" . PHP_EOL .
            "/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;" . PHP_EOL .
            "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;" . PHP_EOL .
            "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;" . PHP_EOL .
            "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;" . PHP_EOL .
            "/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;" . PHP_EOL . PHP_EOL;

        return $ret;
    }
}
