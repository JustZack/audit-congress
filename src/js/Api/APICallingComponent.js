import APICache from "./APICache.js";
import Env from "../Env.js";

export default class APICallingComponent extends React.Component {
    constructor(props) {
      super(props);
      this.state = { };
    }

    apiUrl(route, argsMap) {
      var args = `route=${route}`;
      var argsKeys = Object.keys(argsMap);
      for (var arg in argsKeys) {
        var argName = argsKeys[arg];
        args += `&${argName}=${argsMap[argName]}`;
      }
      return `${Env.getDomain()}api.php?${args}`;
    }

    //Fetch from the backend API
    //Use $route as the top level EX: 'bill'
    //Use options for ordered route options EX: {type: 'hr', number: '8080'}
    async APIFetch(route, options, handleAPIData, updateCache = false) {
      var json, url = this.apiUrl(route, options);
      //Check for route in cache, otherwise fetch
      if (APICache.HasRoute(url) && !updateCache) {
        json = APICache.GetRouteCache(route);
        console.log(`Fetched ${route} from cache`);
      } else {
        console.log(`Fetching ${route} from ${url}`);
        const res = await fetch(url);
        json = await res.json();
        APICache.CacheRoute(url, json);
      }
      
      //Call the handler with the data
      handleAPIData(json);
    }
  }