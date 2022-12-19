<?php

class APICache {
    private static function GetCacheFolder() {
        return __DIR__.DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR;
    }

    private static function EnsureCacheIsCreated() {
        if (!file_exists(APICache::GetCacheFolder())) 
            mkdir(APICache::GetCacheFolder());
    }

    public static function GetIfCached($routeString) {
        APICache::EnsureCacheIsCreated();
        $filename = APICache::GetCacheFolder().$routeString.".json";
        if (file_exists($filename)) {
            return json_decode(file_get_contents($filename), true);
        } else return false;
    }

    public static function CacheRoute($routeString, $data) {
        APICache::EnsureCacheIsCreated();
        $filename = APICache::GetCacheFolder().$routeString.".json";
        if (file_exists($filename)) unlink($filename);
        file_put_contents($filename, json_encode($data));
    }

    public static function UseCache($route, $callback_fn, ...$options) {
        foreach ($options as $op) $route .= ".$op";

        $data = APICache::GetIfCached($route);
        if ($data == false) {
            $data =  call_user_func_array($callback_fn, $options);
            $data["means"] = "API CALL";
            APICache::CacheRoute($route, $data);
        } else {
            $data["means"] = "CACHE";
        }
        return $data;
    }
}

?>