import APICache from "./APICache.js";

export default class APICallingComponent extends React.Component {
    constructor(props) {
      super(props);
      this.state = { };
    }

    //Fetch from the backend API
    //Use $route as the top level EX: 'bill'
    //Use options for ordered route options EX: {type: 'hr', number: '8080'}
    async APIFetch(route, options, handleAPIData, updateCache = false) {
      var optionKeys = Object.keys(options); var json;
      //Convert from options object to query string
      for (var op in optionKeys) route += `&${optionKeys[op]}=${options[optionKeys[op]]}`;
      //Check for route in cache, otherwise fetch
      if (APICache.HasRoute(route) && !updateCache) {
        json = APICache.GetRouteCache(route);
        console.log(`Fetched ${route} from cache`);
      } else {
        var url = `${window.location.href}src/api/api.php?route=${route}`;
        console.log(`Fetching ${route} from ${url}`);
        const res = await fetch(url);
        json = await res.json();
        APICache.CacheRoute(route, json);
      }
      
      //Call the handler with the data
      handleAPIData(json);
    }
  }