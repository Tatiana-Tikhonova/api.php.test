<?php

namespace App\Models;

class ErrorHandler
{
    use \App\SingletonTrait;
    protected function __construct()
    {
    }

    public function handler(int $code, array $error)
    {
        http_response_code($code);
        $res = ["error" => $error];
        return $res;
    }
}
