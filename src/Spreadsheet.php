<?php

namespace ApiSpreadsheets;

use ApiSpreadsheets\Requests\Create;
use ApiSpreadsheets\Requests\Delete;
use ApiSpreadsheets\Requests\Read;
use ApiSpreadsheets\Requests\Update;

/**
 * @method static Create create(string $file_id, array $data, $access_key = null, $secret_key = null)
 * @method static Update update(string $file_id, array $data, string $query, $access_key = null, $secret_key = null)
 * @method static Read read(string $file_id, array $params = [], $access_key = null, $secret_key = null)
 * @method static Delete delete(string $file_id, string $query, $access_key = null, $secret_key = null)
 */

class Spreadsheet
{
    public static function __callStatic($name, $arguments)
    {
        $class = "ApiSpreadsheets\\Requests\\" . ucfirst(strtolower($name));

        if (class_exists($class)) {
            return (new $class(...$arguments))->execute();
        }

        throw new \Exception("Method {$name} not found");
    }
}