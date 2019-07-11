<?php

namespace Yonna\Log;


use Throwable;
use Yonna\Database\DB;

class MongoLog
{

    /**
     * check yonna/database
     * MongoLog constructor.
     */
    public function __construct()
    {
        if (!class_exists(DB::class)) {
            trigger_error('If you want to use mongo log,install composer package yonna/database please.');
        }
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
     * @param string $msg
     */
    private function append($type, $msg = '', array $data = [])
    {
        if (empty($msg) && empty($data)) {
            return;
        }
        $data['type'] = $type;
        $data['record_time'] = time();
        $data['msg'] = $msg;
        try {
            DB::mongo([
                'host' => Config::getMongoHost(),
                'port' => Config::getMongoPort(),
                'account' => Config::getMongoAccount(),
                'password' => Config::getMongoPassword(),
                'name' => Config::getMongoName(),
            ])->collection('log')->insert($data);
        } catch (Throwable $e) {
            (new FileLog())->throwable($e);
        }
        $this->clear();
    }

    /**
     * @param Throwable $t
     */
    public function throwable(Throwable $t)
    {
        $this->append(Type::THROWABLE, $t->getMessage(), [
            'code' => $t->getCode(),
            'file' => $t->getFile(),
            'line' => $t->getLine(),
            'trace' => $t->getTrace(),
        ]);
    }

    /**
     * @param $data
     * @param string $msg
     */
    public function info($msg = '', array $data = [])
    {
        $this->append(Type::INFO, $msg, $data);
    }

    /**
     * @param $data
     * @param string $msg
     */
    public function warning($msg = '', array $data = [])
    {
        $this->append(Type::WARNING, $msg, $data);
    }

    /**
     * @param $data
     * @param string $msg
     */
    public function error($msg = '', array $data = [])
    {
        $this->append(Type::ERROR, $msg, $data);
    }

}