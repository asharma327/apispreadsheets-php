<?php

declare(strict_types=1);

namespace ApiSpreadsheets;

use ApiSpreadsheets\Requests\Create;

/**
 * @method static Create create(string $file_id, array $data, $access_key = null, $secret_key = null)
 */

class Spreadsheet
{
    public static function __callStatic($name, $arguments)
    {
        $class = "ApiSpreadsheets\\Requests\\" . strtolower($name);

        if (class_exists($class)) {
            return (new $class(...$arguments))->execute();
        }

        throw new \Exception("Method {$name} not found");
    }
}