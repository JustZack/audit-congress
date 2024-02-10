<?php

use AuditCongress\ApiObject;

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

    //Just for ease, cant set static array with another var
    private static $secondsIn = [
        "5Min" => 60*5,
        "1Hour" => 60*60,
        "1Day" => 60*60*24,
        "1Week" => 60*60*24*7,
    ];
    private static $cacheIntervalMapping = [
        "bill.list" => 60*5,
        "bills" => 60*60,
        "members" => 60*60*24*7,
    ];
    //Decide how often a given route cache should be invalidated
    private static function DecideCacheInterval($routeString) {
        $interval = 0;
        foreach (APICache::$cacheIntervalMapping as $route=>$value) { 
            if (strpos($routeString, $route) > -1) {
                $interval = APICache::$cacheIntervalMapping[$route];
                break;
            }
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

    public static function CacheRoute(ApiObject $object) {
        APICache::EnsureCacheIsCreated();

        $filename = APICache::GetCacheFilePath($object->getUid().".json");
        if (file_exists($filename)) unlink($filename);

        APICache::UpdateStatusCache($object->getUid());
        APICache::StoreCacheFile($filename, $object);
    }

    public static function UseCache(ApiObject $object) {
        $data = APICache::GetIfCached($object->getUid());

        if ($data == false) {
            $object->fetchFromApi();
            //Filter the data if a function is given
            APICache::CacheRoute($object);
            $data = (array)$object;
            $data["request"]["source"] = "API CALL";
        } else {
            $data["request"]["source"] = "CACHE";
        }
        
        return $data;
    }
}

?>