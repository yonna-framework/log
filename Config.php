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

    //===================================================================

    private static $mongo_host = 'localhost';
    private static $mongo_port = '27017';
    private static $mongo_account = '';
    private static $mongo_password = '';
    private static $mongo_name = 'log';

    /**
     * @return string
     */
    public static function getMongoHost(): string
    {
        return self::$mongo_host;
    }

    /**
     * @param string $mongo_host
     */
    public static function setMongoHost(string $mongo_host): void
    {
        self::$mongo_host = $mongo_host;
    }

    /**
     * @return string
     */
    public static function getMongoPort(): string
    {
        return self::$mongo_port;
    }

    /**
     * @param string $mongo_port
     */
    public static function setMongoPort(string $mongo_port): void
    {
        self::$mongo_port = $mongo_port;
    }

    /**
     * @return string
     */
    public static function getMongoAccount(): string
    {
        return self::$mongo_account;
    }

    /**
     * @param string $mongo_account
     */
    public static function setMongoAccount(string $mongo_account): void
    {
        self::$mongo_account = $mongo_account;
    }

    /**
     * @return string
     */
    public static function getMongoPassword(): string
    {
        return self::$mongo_password;
    }

    /**
     * @param string $mongo_password
     */
    public static function setMongoPassword(string $mongo_password): void
    {
        self::$mongo_password = $mongo_password;
    }

    /**
     * @return string
     */
    public static function getMongoName(): string
    {
        return self::$mongo_name;
    }

    /**
     * @param string $mongo_name
     */
    public static function setMongoName(string $mongo_name): void
    {
        self::$mongo_name = $mongo_name;
    }

}