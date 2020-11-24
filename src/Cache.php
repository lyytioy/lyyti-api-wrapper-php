<?php
declare(strict_types=1);
namespace Lyyti\API\v2\Client;

class Cache {
    private $lifetime_minutes, $cache_file_path, $cache;
    
    public function __construct(int $lifetime_minutes = 10, ?string $cache_file_path = null) {
        $this->lifetime_minutes = $lifetime_minutes;
        $this->cache_file_path = $cache_file_path;

        $this->cache = array();

        if (isset($cache_file_path) && file_exists($cache_file_path)) {
            foreach (json_decode(file_get_contents($cache_file_path), true) as $call_string => $response) {
                $this->cache[$call_string] = CachedResponse::fromArray($response);
            }
        }
    }

    public function cacheResponse(string $call_string, Response $response) {
        $this->cache[$call_string] = new CachedResponse($response);
        if (isset($this->cache_file_path)) file_put_contents($this->cache_file_path, json_encode($this->cache));
    }

    public function getCachedResponse(string $call_string) {
        if (array_key_exists($call_string, $this->cache)) {
            $cached_response = (object) $this->cache[$call_string];
            if (!$this->cacheIsExpired($cached_response)) {
                return $cached_response->response;
            }
        }
        $this->removeExpiredCaches();
    }

    private function cacheIsExpired(CachedResponse $cached_response) {
        return $cached_response->timestamp + $this->lifetime_minutes * 60 < time();
    }

    private function removeExpiredCaches() {
        $this->cache = array_filter($this->cache, function ($value) {
            return !$this->cacheIsExpired($value);
        });
    }
}