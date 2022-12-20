<?php

class APICache {    
    private static function GetCacheFolder() {
        return __DIR__.DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR;
    }

    private static function GetCacheFilePath($filename) {
        return APICache::GetCacheFolder()."$filename";
    }

    private static function EnsureCacheIsCreated() {
        //Create cache folder
        $cacheFolder = APICache::GetCacheFolder();
        if (!file_exists($cacheFolder)) 
            mkdir($cacheFolder);

        $filename = APICache::GetCacheFilePath("status.json");
        //Create status file
        if (!file_exists($filename))
            APICache::StoreCacheFile($filename, array("created"=> time(), "items" => array()));
    }

    private static function GetCacheFile($filename) {
        return json_decode(file_get_contents($filename), true);
    }

    private static function StoreCacheFile($filename, $data) {
        return file_put_contents($filename, json_encode($data));
    }

    //Decide how often a given route cache should be invalidated
    private static function DecideCacheInterval($routeString) {
        //Update after a day
        if (strpos($routeString, "bill")) return 604800;
        //Update after 5 minutes
        else if (strpos($routeString, "recent.bills")) return 300;
    }

    private static function UpdateStatusCache($routeString) {
        $status = APICache::GetIfCached("status");
        $status["items"][$routeString] = array("created" => time(), "updateEvery" => APICache::DecideCacheInterval($routeString));
        APICache::StoreCacheFile(APICache::GetCacheFilePath("status.json"), $status);
    }

    public static function GetIfCached($routeString) {
        APICache::EnsureCacheIsCreated();

        $filename = APICache::GetCacheFilePath($routeString.".json");
        if (file_exists($filename)) return APICache::GetCacheFile($filename);
        else return false;
    }

    public static function CacheRoute($routeString, $data) {
        APICache::EnsureCacheIsCreated();

        $filename = APICache::GetCacheFilePath($routeString.".json");
        if (file_exists($filename)) unlink($filename);

        APICache::UpdateStatusCache($routeString);
        APICache::StoreCacheFile($filename, $data);
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