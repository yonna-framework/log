<?php

namespace Yonna\Log;


use Throwable;

class FileLog
{

    private $root = null;

    public function __construct()
    {
        $this->root = realpath(__DIR__ . '/../../../');
    }

    /**
     * 递归删除过期日志
     * @param $dir
     * @param integer $timestamp 删除这一天之前的
     */
    public function dirExpire($dir, $timestamp)
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = opendir($dir);
        while (false !== ($file = readdir($files))) {
            if ($file != '.' && $file != '..') {
                $t = strtotime($file);
                if ($t > $timestamp) {
                    continue;
                }
                $realDir = realpath($dir);
                $realFile = $realDir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($realFile)) {
                    static::dirExpire($realFile, $timestamp);
                    @rmdir($realFile);
                } else {
                    @unlink($realFile);
                }
            }
        }
        closedir($files);
        if ($dir !== $this->root . DIRECTORY_SEPARATOR . Config::getFile()) {
            @rmdir($dir);
        }
    }

    /**
     * 清除文件日志
     */
    private function clear()
    {
        if (Config::getFileExpireDay() <= 0) {
            return;
        }
        $this->dirExpire($this->root . DIRECTORY_SEPARATOR . Config::getFile(), time() - 86400 * Config::getFileExpireDay());
    }

    /**
     * 获取日志目录，以天分割
     * @return string
     */
    private function dir()
    {
        $path = $this->root
            . DIRECTORY_SEPARATOR . Config::getFile()
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