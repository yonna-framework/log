<?php

namespace Yonna\Log;

use Yonna\IO\Request;
use Yonna\Scope\Config;
use Yonna\Log\Config as LogConf;

class Scope
{

    private static function myScanDir($dir)
    {
        $file_arr = scandir($dir);
        $new_arr = [];
        foreach ($file_arr as $f) {
            if ($f != ".." && $f != ".") {
                if (is_dir($dir . "/" . $f)) {
                    $new_arr[$f] = self::myScanDir($dir . "/" . $f);
                } else {
                    $new_arr[] = $f;
                }
            }
        }
        return $new_arr;
    }

    public static function conf()
    {
        Config::group(['log'], function () {
            Config::post('dir', function () {
                $dir = realpath(LogConf::getDir() . LogConf::getFile());
                $dir = self::myScanDir($dir);
                return $dir;
            });
            Config::post('file', function (Request $request) {
                $file = realpath(LogConf::getDir() . LogConf::getFile() . DIRECTORY_SEPARATOR . $request->getInput()['file']);
                if (!is_file($file)) {
                    return '';
                }
                return file_get_contents($file);
            });
            Config::post('page', function (Request $request) {
                $input = $request->getInput();
                return Log::db()->page(
                    $input['current'] ?? 1,
                    $input['per'] ?? 10,
                    [
                        'unique_key' => $input['unique_key'] ?? null,
                        'source' => $input['source'] ?? null
                    ],
                    );
            });
        });
    }

}