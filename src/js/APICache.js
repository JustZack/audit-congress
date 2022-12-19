 export default class APICache {
    static cache = {};
    static CacheRoute(route, json) {
        APICache.cache[route] = json;
    }
    static HasRoute(route) {
        return APICache.cache[route] !== undefined;
    }
    static GetRouteCache(route) {
        return APICache.cache[route];
    }
 }