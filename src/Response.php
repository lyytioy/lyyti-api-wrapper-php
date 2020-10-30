<?php
declare(strict_types=1);
namespace LyytiApi;

class Response {
    public $data, $http_code, $error;

    function __construct($data, int $http_code, ?string $error = null)
    {
        $this->data = $data;
        $this->http_code = $http_code;
        $this->error = $error;
    }
}