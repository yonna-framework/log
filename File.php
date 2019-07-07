<?php

namespace Yonna\Log;


use RuntimeException;
use Throwable;

class File
{

    private $root = null;

    public function __construct(string $root)
    {
        if (!is_dir($root)) {
            throw new RuntimeException('error log root path');
        }
        $this->root = realpath($root);
    }

    /**
     * 获取日志目录，以天分割
     * @return string
     */
    private function dir()
    {
        $path = $this->root
            . DIRECTORY_SEPARATOR . 'applog'
            . DIRECTORY_SEPARATOR . date('Y-m-d');
        $temp = str_replace('\\', '/', $path);
        $p = explode('/', $temp);
        $tempLen = count($p);
        $temp = '';
        for ($i = 0; $i < $tempLen; $i++) {
            $temp .= $p[$i] . DIRECTORY_SEPARATOR;
            if (!is_dir($temp)) {
                @mkdir($temp);
                @chmod($temp, 0777);
            }
        }
        $temp = realpath($temp) . DIRECTORY_SEPARATOR;
        return $temp ? $temp : false;
    }

    /**
     * 获取日志文件名
     * @param $type
     * @return string
     */
    private function file($type)
    {
        return strtolower($type) . '.log';
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
        $append = 'time:' . date("Y-m-d H:i:s D T") . PHP_EOL;
        $msg && $append .= 'msg:' . $msg . PHP_EOL;
        if ($data) {
            $append .= 'data:' . PHP_EOL;
            foreach ($data as $k => $v) {
                if (is_array($v) || is_object($v)) {
                    $v = json_encode($v, JSON_UNESCAPED_UNICODE);
                } else {
                    $v = (string)$v;
                }
                $data && $append .= " #{$k} " . $v . PHP_EOL;
            }
        }
        @file_put_contents($this->dir() . $this->file($type), $append . PHP_EOL, FILE_APPEND);
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