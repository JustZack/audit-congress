<?php 

namespace CongressGov {
    require_once AUDITCONGRESS_FOLDER."/abstract.api.php";
    class Api extends \AuditCongress\Api {
        private static
        $api_key,
        $api_item_limit = 75,
        $api_base_url = "https://api.congress.gov/v3/",
        $api_query_args,
        $api_url,
        $api_title;
        

        //Initialize private members
        public static function init() {
            Api::$api_key = file_get_contents(CONGRESSGOV_FOLDER."/api.congress.key");
            Api::$api_query_args = "?api_key=".Api::$api_key."&format=json";
            Api::$api_url = Api::$api_base_url . "%s" . Api::$api_query_args;
            Api::$api_title = "CongressGov";
        }

        //Fetch and parse JSON API data
        //The base level of all API functions
        static function get($url) {
            if (!Api::$api_key) Api::noApiKeySet($url, Api::$api_title);
            $json = Api::doApiGet($url);
            return Api::doApiGetReturn($json, $url, Api::$api_title);
        }

        //Make an API call with the given route and options
        //Defaults to 20 items per request
        static function call($route, $required_field = null, $additional_args = null) {//, $options) {
            $url = sprintf(Api::$api_url, $route);
            if ($additional_args !== null) $url .= "&$additional_args";
            $json = Api::get($url);
            return Api::doApiCallReturn($json, $required_field, $url, Api::$api_title);
        }

        private static $lastBulkCallTotal = -1;
        static function getLastBulkCallTotal() { return Api::$lastBulkCallTotal; }
        //Make an API call with the given route and options
        //Pulls all items for this route via the pagination property
        static function call_bulk($route, $required_field = null, $itemLimit = 750, $additionalArgs = null) {
            $full_route_json = []; $json = "";
            $url = sprintf(Api::$api_url, $route);
            //Keep track of the record offset for pagination
            $offset = 0; $pageLimit = Api::$api_item_limit; $doneCalling = false;
            if ($itemLimit < $pageLimit) $pageLimit = $itemLimit;
            //Fetch API pages while pages exist
            do {
                //Make the API call with offset and limit arguments appended
                if ($offset + $pageLimit > $itemLimit) $pageLimit = $itemLimit - $offset;
                $args = "&offset=$offset&limit=$pageLimit&$additionalArgs";
                $json = Api::get($url . $args);
                //If this is the first run
                //Determine which key stores the data in this response
                //Based on all requests having at most [pagination, request, $data_array]
                if ($offset == 0) $data_array_name = array_values(array_diff(array_keys($json), ["pagination", "request"]))[0];
                //if ($offset == 0) $data_array_name = $required_field;

                //Increment offset by itme limit amount
                $offset += $pageLimit;

                //Merge existing route data with new data
                $full_route_json = array_merge($full_route_json, $json[$data_array_name]);

                //At or exceeded items limit
                if ($offset >= $itemLimit) $doneCalling = true;
                //No pagination property => no additional pages to fetch
                else if (!isset($json["pagination"])) $doneCalling = true;
                //Total less than offset => No more pages exist
                else if ($json["pagination"]["count"] < $offset) $doneCalling = true; 
            } while (!$doneCalling);

            //Store the full dataset in the json response
            $json[$data_array_name] = $full_route_json;
            Api::$lastBulkCallTotal = $json["pagination"]["count"];
            //Remove pagination section since paging isn't nessesary for this request (anymore)
            unset($json["pagination"]);
            unset($json["request"]);

            return Api::doApiCallReturn($json, $required_field, $url, Api::$api_title);
        }


    }
    //Initialize private members
    API::init();
}

?>