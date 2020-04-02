<?php

namespace Yonna\Log;

use Yonna\IO\Request;
use Yonna\Scope\Config;

class Scope
{

    public static function conf()
    {
        Config::post('file', function () {
            return Log::file()->get();
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
    }

}