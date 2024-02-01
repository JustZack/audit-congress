<?php 

namespace CongressGov {
    require_once AUDITCONGRESS_FOLDER."/abstract.api.php";
    class Api extends \AuditCongress\Api {
        private static
        $api_key,
        $api_item_limit = 20,
        $api_base_url = "https://api.congress.gov/v3/",
        $api_query_args,
        $api_url;

        //Initialize private members
        public static function init() {
            Api::$api_key = file_get_contents("api.congress.key");
            Api::$api_query_args = "?api_key=".Api::$api_key."&format=json";
            Api::$api_url = Api::$api_base_url . "%s" . Api::$api_query_args;
        }

        //Fetch and parse JSON API data
        //The base level of all API functions
        static function get($url) {
            if (!Api::$api_key) print_r("Error: API Key not set");
            $json = @file_get_contents($url);
            if ($json === false) throw new \Exception("Request failed: $url");
            return json_decode($json, true);
        }

        //Make an API call with the given route and options
        //Defaults to 20 items per request
        static function call($route, $additional_args = null) {//, $options) {
            $url = sprintf(Api::$api_url, "$route");
            if ($additional_args !== null) $url .= "&$additional_args";
            $json = Api::get($url);
            return $json;
        }

        //Make an API call with the given route and options
        //Pulls all items for this route via the pagination property
        static function call_bulk($route, $options, $additional_args = null) {
            $full_route_json = []; 
            //$json; $data_array_name;
            $url = sprintf(Api::$api_url, "$route/$options");
            if ($additional_args !== null) $url .= "&$additional_args";
            //Keep track of the record offset for pagination
            $offset = 0; $doneCalling = false;
            //Fetch API pages while pages exist
            do {
                //Make the API call with offset and limit arguments appended
                $args = "&offset=$offset&limit=".Api::$api_item_limit;
                $json = Api::get($url . $args);
                //If this is the first run
                //Determine which key stores the data in this response
                //Based on all requests having at most [pagination, request, $data_array]
                if ($offset == 0) $data_array_name = array_values(array_diff(array_keys($json), ["pagination", "request"]))[0];
                //Increment offset by itme limit amount
                $offset += Api::$api_item_limit;
                //No pagination property => no additional pages to fetch
                if (!isset($json["pagination"])) $doneCalling = true;
                //Fewer items left then the next item offset
                // => No more pages exist
                else if ($json["pagination"]["count"] < $offset) $doneCalling = true; 
                //Merge existing route data with new data
                $full_route_json = array_merge($full_route_json, $json[$data_array_name]);
            } while (!$doneCalling);

            //Store the full dataset in the json response
            $json[$data_array_name] = $full_route_json;
            //Remove pagination section since paging isn't nessesary for this request (anymore)
            unset($json["pagination"]);

            return $json;
        }
    }
    //Initialize private members
    API::init();
}

?>