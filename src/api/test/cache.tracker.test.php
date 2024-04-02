<?php

namespace APITest {
    class CacheTracker {
        static function testGetCache($cacheName) {
            return new \AuditCongress\CacheTracker($cacheName);
        }

        static function testGetCacheNextUpdate($cacheName) {
            $ct = self::testGetCache($cacheName);
            $next = $ct->getNextCacheUpdate();
            var_dump($ct->cacheSettings);
            var_dump($next);
        }

        static function testRunCacheScript($cacheName) {
            $ct = self::testGetCache($cacheName);
            $output = $ct->runCachingScript();
            var_dump($output);
        }
    }
}
?>
