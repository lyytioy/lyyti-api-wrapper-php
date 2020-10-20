<?php
declare(strict_types=1);
namespace LyytiApi;

class CachedResponse {
    public $timestamp, $response;

    function __construct(string $response)
    {
        $this->timestamp = time();
        $this->response = $response;
    }
}