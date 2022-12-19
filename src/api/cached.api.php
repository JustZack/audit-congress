<?php

require "congress.api/congress.api.php";

class CachedAPI {
    private static $cacheRoot = false;
    private static function verifyCacheRootIsSet() {
        if (CachedAPI::$cacheRoot == false) CachedAPI::setCacheRoot();
    }
    public static function CacheRoot() {
        CachedAPI::verifyCacheRootIsSet();
        return CachedAPI::$cacheRoot;
    }
    private static function setCacheRoot() {
        CachedAPI::$cacheRoot = dirname(__DIR__)."\cache\\";
    }



    private static function directory_exists($dir) {
        return file_exists($dir) && is_dir($dir);
    }
    private static function hasCacheFile($relativePath) {
        return file_exists(CachedAPI::CacheRoot().$relativePath);
    }
    private static function getCacheFile($relativePath) {
        if (CachedAPI::hasCacheFile($relativePath)) {
            return file_get_contents(CachedAPI::CacheRoot().$relativePath);
        } else {
            return false;
        }

        print_r(CachedAPI::CacheRoot().$relativePath);
    }


    
    private static function createNewStatusFile() {
        $now = time();
        $statusjson = '{"created" : "'.$now.'"}';
        file_put_contents(CachedAPI::CacheRoot()."status.json", $statusjson);
    }
    private static function verifyCacheStructure() {
        if (!CachedAPI::directory_exists(CachedAPI::CacheRoot())) mkdir(CachedAPI::CacheRoot());
        if (!CachedAPI::hasCacheFile("status.json")) CachedAPI::createNewStatusFile();
    }
    private static function verifyCacheStatus() {
        //Check every entry in status.json for invalidation
    }
    private static function verifyCachedData($route) {
        CachedAPI::verifyCacheStructure();
        //Check if the given route cache needs to be updated
    }



    private static function getCachedAPIData($route) {
        CachedAPI::verifyCacheStructure();
        //return saved data;   
        /*
            1. make sure the route is cached
                a. firstly via status.json, to verify it has been cached
                b. then ensure the actual file exists
            2. read cached route data
            3. make sure data is valid
        */
    }
    private static function routeToCacheFile($route) {
        $path = str_replace("/",".",$route.".json");
        if ($path[0] == '.') $path = substr($path, 1);
        return CachedAPI::CacheRoot().$path;
    }
    public static function StoreAPIData($route, $json) {
        //Create a cache for the given route and response
        //IE: Create /cache/bill.hr.8084.json
        //OR: Create /cache/bill/hr/8084.json
        
        //Very little data will change after being cached:
        //Bills have unchangable data, such as creation date, ID, type, etc.
        //      => Data that does change is meta, such as number of actions/cosponsors/committee-reports
        //      => And even the actual data behind those will only be fetched once
        //Actions cannot be reversed, thus only need to be fetched once.
        //Votes/Roll calls are historical records, which will not change
        //Member data could change when terms end/begin
        //      => so only invalidate after elections?

        //Essentailly, most congressional data is factual, and exists as a record.
        //All cache data should be tracked so it is known when it was last updated.
        //But most cache data will never need to be updated
    }
    public static function GetAPIData($route) {
        //Return api data from cache
        //I.E:
        /*
        Check if cache/$route exists
        true: return it
        false: fetch, cache, return it
        */
        
        //Does the cache already exist?
        CachedAPI::verifyCacheStructure();
        if (CachedAPI::HasAPIData($route)) {

        } else {
            
        }

        print_r(CachedAPI::routeToCacheFile($route));
    }
    public static function HasAPIData($route) {
        //Check if this route has been cached   
        //  exists(cache/$route)
        return file_exists(CachedAPI::routeToCacheFile($route));
    }
}

?>