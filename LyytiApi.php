<?php

class LyytiApi
{
    private $api_root = "https://api.lyyti.com/v2/";
    private $private_key, $public_key;

    public function __construct($private_key, $public_key)
    {
        $this->private_key = $private_key;
        $this->public_key = $public_key;
    }

    private function getAuthHeader($call_string)
    {
        $timestamp = time();

        $msg = implode(',', [
            $this->public_key,
            $timestamp,
            $call_string,
        ]);

        $signature = hash_hmac(
            'sha256',
            base64_encode($msg),
            $this->private_key
        );

        return "Authorization: LYYTI-API-V2 public_key=$this->public_key, timestamp=$timestamp, signature=$signature";
    }

    public function get($call_string)
    {
        $headers = [
            "Accept: application/json; charset=utf-8",
            $this->getAuthHeader($call_string)
        ];

        $ch = curl_init($this->api_root.$call_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function getEvents()
    {
        $response = $this->get("events?as_array=1");
        return json_decode($response)->results;
    }
}