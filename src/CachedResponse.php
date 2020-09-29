<?php namespace LyytiApi;

class CachedResponse {
    public $timestamp, $response;

    function __construct($response)
    {
        $this->timestamp = time();
        $this->response = $response;
    }
}