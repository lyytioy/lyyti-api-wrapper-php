<?php

include "CachedResponse.php";

class LyytiApi
{
    private $private_key, $public_key, $cache_enabled, $cache_lifetime_minutes;
    private $api_root = "https://api.lyyti.com/v2/";
    private $response_cache = array();

    public function __construct($private_key, $public_key, $cache_enabled = true, $cache_lifetime_minutes = 10)
    {
        $this->private_key = $private_key;
        $this->public_key = $public_key;
        $this->cache_enabled = $cache_enabled;
        $this->cache_lifetime_minutes = $cache_lifetime_minutes;
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

    private function getResponseFromCache($call_string) {
        if (array_key_exists($call_string, $this->response_cache)) {
            $cached_response = $this->response_cache[$call_string];
            if (!$this->cacheIsExpired($cached_response)) {
                return $cached_response->response;
            }
        }
        $this->removeExpiredCaches();
        return null;
    }
    
    private function cacheIsExpired($cached_response) {
        return $cached_response->timestamp + $this->cache_lifetime_minutes * 60 < time();
    }

    private function removeExpiredCaches() {
        $this->response_cache = array_filter($this->response_cache, function ($value) {
            return !$this->cacheIsExpired($value);
        });
    }

    public function get($call_string)
    {
        if ($this->cache_enabled) {
            $cached_response = $this->getResponseFromCache($call_string);
            if ($cached_response != null) return $cached_response;
        }

        $headers = [
            "Accept: application/json; charset=utf-8",
            $this->getAuthHeader($call_string)
        ];

        $ch = curl_init($this->api_root.$call_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
        curl_close($ch);

        if ($this->cache_enabled) {
            $this->response_cache[$call_string] = new CachedResponse($data);
        }

        return $data;
    }

    public function getEvents()
    {
        $response = $this->get("events?as_array=1");
        return json_decode($response)->results;
    }

    public function getParticipants($event)
    {
        $response = $this->get("events/$event->event_id/participants?as_array=1&show_answers=1");
        return json_decode($response)->results;
    }

    public function getStandardQuestions($event = null) {
        $call_string = "standard_questions?as_array=1";
        if ($event != null) $call_string .= "&event_id=$event->event_id";
        $response = $this->get($call_string);
        return json_decode($response)->results;
    }
}