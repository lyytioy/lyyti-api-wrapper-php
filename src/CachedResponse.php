<?php
declare(strict_types=1);
namespace Lyyti\API\v2\Client;

class CachedResponse {
    public $timestamp, $response;

    function __construct(string $response)
    {
        $this->timestamp = time();
        $this->response = $response;
    }
}