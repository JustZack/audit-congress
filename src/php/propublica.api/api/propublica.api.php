<?php 

/*
    Queries endpoints found on:
        https://projects.propublica.org/api-docs/congress-api/
*/

namespace ProPublica {
    require_once AUDITCONGRESS_FOLDER."/abstract.api.php";
    class Api extends \AuditCongress\Api {
        private static
            $api_key,
            $api_headers,
            $api_item_limit = 20,
            $api_base_url = "https://api.propublica.org/congress/v1/",
            $api_url,
            $api_title;

        //Initialize private members
        public static function init() {
            API::$api_key = file_get_contents(PROPUBLICA_FOLDER."/api.propublica.key");
            $key = API::$api_key;
            API::$api_headers = stream_context_create(["http" => ["method" => "GET", "header" => "X-API-Key: $key\r\n"]]);
            API::$api_url = Api::$api_base_url . "%s";
            Api::$api_title = "ProPublica";
        }

        //Fetch and parse JSON API data
        //The base level of all API functions
        static function get($url) {
            if (!Api::$api_key || !Api::$api_headers) Api::noApiKeySet($url, Api::$api_title);
            $json = Api::doApiGet($url, Api::$api_headers);
            return Api::doApiGetReturn($json, $url, Api::$api_title);
        }

        //Make an API call with the given route and options
        //Defaults to 20 items per request
        static function call($route, $required_field, $additional_args = null) {//, $options) {
            $url = sprintf(Api::$api_url, $route);
            if ($additional_args !== null) $url .= "&$additional_args";
            $json = Api::get($url);
            
            return Api::doApiCallReturn($json, $required_field, $url, Api::$api_title);
        }

        //Make an API call with the given route and options
        //Pulls all items for this route via the pagination property
        static function call_bulk($route, $required_field, $additional_args = null) {
            $full_route_json = []; 
            //$json; $data_array_name;
            $url = sprintf(Api::$api_url, $route);
            if ($additional_args !== null) $url .= "&$additional_args";
            //Keep track of the record offset for pagination
            $offset = 0; $doneCalling = false;
            //Fetch API pages while pages exist
            do {
                //Make the API call with offset and limit arguments appended
                $args = "?offset=$offset";
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

            return Api::doApiCallReturn($json, $required_field, $url, Api::$api_title);
        }
    }
    //Initialize private members
    API::init();
}

?>
