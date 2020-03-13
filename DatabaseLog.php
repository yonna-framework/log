<?php

namespace Yonna\Log;


use Throwable;
use Yonna\Database\DB;
use Yonna\Database\Driver\Mongo;
use Yonna\Database\Driver\Mysql;
use Yonna\Database\Driver\Pgsql;
use Yonna\Database\Driver\Type as DBType;

class DatabaseLog
{

    private $store = 'yonna_log';
    private $config = null;

    /**
     * check yonna/database
     * DatabaseLog constructor.
     */
    public function __construct()
    {
        if (!class_exists(DB::class)) {
            trigger_error('If you want to use database log,install composer package yonna/database please.');
            return;
        }
        if (Config::getDatabase() === null) {
            trigger_error('Set Database for DatabaseLog.');
            return;
        }
        $this->config = Config::getDatabase();
    }

    /**
     * 清除日志
     */
    private function clear()
    {
        if (Config::getFileExpireDay() <= 0) {
            return;
        }
    }

    /**
     * 写入日志
     * @param $type
     * @param array $data
     * @param string $key
     */
    private function append($type, $key, array $data = [])
    {
        if (empty($key) && empty($data)) {
            return;
        }
        $db = DB::connect($this->config);
        $logData = [
            'key' => $key,
            'type' => $type,
            'log_time' => time(),
            'data' => $data,
        ];
        try {
            if ($db instanceof Mongo) {
                $db->collection("{$this->store}_" . $key)->insert($logData);
            } elseif ($db instanceof Mysql) {
                $db->query("CREATE TABLE IF NOT EXISTS `{$this->store}`(
                        `id` bigint NOT NULL AUTO_INCREMENT COMMENT 'id',
                        `key` char(255) NOT NULL DEFAULT 'default' COMMENT 'key',
                        `type` char(255) NOT NULL DEFAULT 'info' COMMENT '类型',
                        `log_time` int NOT NULL COMMENT '时间戳',
                        `data` json COMMENT 'data',
                        PRIMARY KEY (`id`)
                    ) ENGINE = INNODB COMMENT 'log by yonna';");
                $db->table($this->store)->insert($logData);
            } elseif ($db instanceof Pgsql) {
                $db->query("CREATE TABLE IF NOT EXISTS `{$this->store}`(
                        `id` bigserial NOT NULL,
                        `key` text NOT NULL DEFAULT 'default',
                        `type` text NOT NULL DEFAULT 'info',
                        `log_time` integer NOT NULL,
                        `data` jsonb,
                        PRIMARY KEY (`id`)
                    ) ENGINE = INNODB COMMENT 'log by yonna';");
                $db->table($this->store)->insert($logData);
            } else {
                throw new \Exception('Set Database for Support Driver.');
            }
        } catch (Throwable $e) {
            Log::file()->throwable($e);
        }

        $this->clear();
    }

    /**
     * @param string $key
     * @param Throwable $t
     */
    public
    function throwable(Throwable $t, $key = 'default')
    {
        $this->append(Type::THROWABLE, $key, [
            'code' => $t->getCode(),
            'message' => $t->getMessage(),
            'file' => $t->getFile(),
            'line' => $t->getLine(),
            'trace' => $t->getTrace(),
        ]);
    }

    /**
     * @param array $data
     * @param string $key
     */
    public
    function info(array $data = [], $key = 'default')
    {
        $this->append(Type::INFO, $key, $data);
    }

    /**
     * @param array $data
     * @param string $key
     */
    public
    function warning(array $data = [], $key = 'default')
    {
        $this->append(Type::WARNING, $key, $data);
    }

    /**
     * @param array $data
     * @param string $key
     */
    public
    function error(array $data = [], $key = 'default')
    {
        $this->append(Type::ERROR, $key, $data);
    }

}