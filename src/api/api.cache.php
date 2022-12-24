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

    private static $cacheIntervalMapping = [
        "recent.bills" => 300,
        "bill" => 604800,
        "member" => 604800*7,
    ];
    //Decide how often a given route cache should be invalidated
    private static function DecideCacheInterval($routeString) {
        $interval = 0;
        foreach (APICache::$cacheIntervalMapping as $route=>$value) { 
            if (strpos($routeString, $route) > -1) $interval = APICache::$cacheIntervalMapping[$route];
        }
        return $interval;
    }

    private static function GetStatusFile() {
        APICache::EnsureCacheIsCreated();
        $filename = APICache::GetCacheFilePath("status.json");
        return APICache::GetCacheFile($filename);
    }

    private static function UpdateStatusCache($routeString) {
        $status = APICache::GetStatusFile();
        $status["items"][$routeString] = 
                array(  "created" => time(), 
                        "updateEvery" => APICache::DecideCacheInterval($routeString)
                );
        APICache::StoreCacheFile(APICache::GetCacheFilePath("status.json"), $status);
    }

    private static function RouteExistsAndIsValid($routeString) {
        $filename = APICache::GetCacheFilePath($routeString.".json");
        ///Cache file exists
        if (file_exists($filename)) {
            $status = APICache::GetStatusFile();
            //Status knows of cache
            if (isset($status["items"][$routeString])) {
                $route = $status["items"][$routeString];
                //Cache file isnt too old
                if (time() < $route["created"]+$route["updateEvery"]) {
                    return true;
                } else {
                    unset($filename);
                }
            }
        }
        return false;
    }

    public static function GetIfCached($routeString) {
        APICache::EnsureCacheIsCreated();

        $filename = APICache::GetCacheFilePath($routeString.".json");
        if (APICache::RouteExistsAndIsValid($routeString))
             return APICache::GetCacheFile($filename);
        else return false;
    }

    public static function CacheRoute($routeString, $data) {
        APICache::EnsureCacheIsCreated();

        $filename = APICache::GetCacheFilePath($routeString.".json");
        if (file_exists($filename)) unlink($filename);

        APICache::UpdateStatusCache($routeString);
        APICache::StoreCacheFile($filename, $data);
    }

    public static function UseCache($route, $filter_function, $api_function, ...$options) {
        foreach ($options as $op) if (isset($op)) $route .= ".$op";

        $data = APICache::GetIfCached($route);

        if ($data == false) {
            $data =  call_user_func_array($api_function, $options);
            //Filter the data if a function is given
            if (strlen($filter_function) > 0) 
            $data = call_user_func($filter_function, $data);
            APICache::CacheRoute($route, $data);
            $data["means"] = "API CALL";
        } else {
            $data["means"] = "CACHE";
        }
        
        return $data;
    }
}

?>