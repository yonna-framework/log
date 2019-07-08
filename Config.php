<?php

namespace Yonna\Log;

class Config
{

    /**
     * 日志文件目录名
     * @var string
     */
    private static $file = 'applog';

    /**
     * 文件日志的过期天数
     * @var int
     */
    private static $file_expire_day = 0; // day

    /**
     * @return string
     */
    public static function getFile(): string
    {
        return self::$file;
    }

    /**
     * @param string $file
     */
    public static function setFile(string $file): void
    {
        self::$file = $file;
    }

    /**
     * @return int
     */
    public static function getFileExpireDay(): int
    {
        return self::$file_expire_day;
    }

    /**
     * @param int $file_expire_day
     */
    public static function setFileExpireDay(int $file_expire_day): void
    {
        self::$file_expire_day = $file_expire_day;
    }


}