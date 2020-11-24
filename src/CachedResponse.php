<?php
declare(strict_types=1);
namespace Lyyti\API\v2\Client;

class CachedResponse {
    public $timestamp, $response;

    function __construct(Response $response, ?int $timestamp = null)
    {
        $this->response = $response;

        if (isset($timestamp)) $this->timestamp = $timestamp;
        else $this->timestamp = time();
    }

    static function fromArray(array $input) {
        return new CachedResponse(Response::fromArray($input["response"]), $input["timestamp"]);
    }
}