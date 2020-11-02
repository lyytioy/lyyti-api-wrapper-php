<?php
declare(strict_types=1);
namespace LyytiApi;

class Client
{
    private const API_ROOT = "https://api.lyyti.com/v2/";
    private $private_key, $public_key, $cache_enabled, $cache_lifetime_minutes;
    private $response_cache = array();

    public function __construct(string $private_key, string $public_key, bool $cache_enabled = true, int $cache_lifetime_minutes = 10)
    {
        $this->private_key = $private_key;
        $this->public_key = $public_key;
        $this->cache_enabled = $cache_enabled;
        $this->cache_lifetime_minutes = $cache_lifetime_minutes;
    }

    private function getAuthHeader(string $call_string)
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

        return "Authorization: LYYTI-API-V2 public_key={$this->public_key}, timestamp={$timestamp}, signature={$signature}";
    }

    private function getResponseFromCache(string $call_string) {
        if (array_key_exists($call_string, $this->response_cache)) {
            $cached_response = $this->response_cache[$call_string];
            if (!$this->cacheIsExpired($cached_response)) {
                return $cached_response->response;
            }
        }
        $this->removeExpiredCaches();
    }
    
    private function cacheIsExpired(CachedResponse $cached_response) {
        return $cached_response->timestamp + $this->cache_lifetime_minutes * 60 < time();
    }

    private function removeExpiredCaches() {
        $this->response_cache = array_filter($this->response_cache, function ($value) {
            return !$this->cacheIsExpired($value);
        });
    }

    public function get(string $call_string, array $params = array())
    {
        $call_string = $call_string."?".http_build_query($params);

        if ($this->cache_enabled) {
            $cached_response = $this->getResponseFromCache($call_string);
            if ($cached_response != null) return $cached_response;
        }

        $headers = [
            "Accept: application/json; charset=utf-8",
            $this->getAuthHeader($call_string)
        ];

        $ch = curl_init($this::API_ROOT.$call_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
        $http_code = curl_getinfo($ch)["http_code"];
        curl_close($ch);

        if ($this->cache_enabled) {
            $this->response_cache[$call_string] = new CachedResponse($data);
        }

        return $response = new Response($data, $http_code);
    }

    public function getEvents()
    {
        $response = $this->get("events", ["as_array" => 1]);
        if ($response->http_code >= 300) return new Response(null, $response->http_code, json_decode($response->data)->error);
        return new Response(json_decode($response->data)->results, $response->http_code);
    }

    public function getParticipants(object $event)
    {
        $response = $this->get("events/{$event->event_id}/participants", ["as_array" => 1, "show_answers" => 1]);
        if ($response->http_code >= 300) return new Response(null, $response->http_code, json_decode($response->data)->error);
        return new Response(json_decode($response->data)->results, $response->http_code);
    }

    public function getStandardQuestions(object $event = null) {
        $params = ["as_array" => 1];
        if ($event != null) $params["event_id"] = $event->event_id;
        $response = $this->get("standard_questions", $params);
        if ($response->http_code >= 300) return new Response(null, $response->http_code, json_decode($response->data)->error);
        return new Response(json_decode($response->data)->results, $response->http_code);
    }
}
