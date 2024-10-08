<?php 

namespace CongressGov {
    require_once AUDITCONGRESS_FOLDER."/abstract.api.php";
    require_once AUDITCONGRESS_FOLDER."/api.request.php";
    class Api extends \AuditCongress\Api {      
        public static 
            $api_key,
            $api_key_id,
            $api_item_limit,
            $api_base_url,
            $api_query_args,
            $api_url,
            $api_headers,
            $api_title;

        public static function createRequest($url) {
            return new \AuditCongress\ApiRequest($url);
        }

        //Initialize private members
        public static function init() {
            Api::$api_base_url = "https://api.congress.gov/v3/";
            
            //Old way of reading an api key file
            //Api::$api_key = file_get_contents(CONGRESSGOV_FOLDER."/api.congress.key");
            //New way of asking for a useable token from the APIService Tracker
            $token = \APIService\Tracker::getUsableToken("api.congress.gov");
            Api::$api_key = $token["token"];
            Api::$api_key_id = $token["id"];

            Api::$api_query_args = "?api_key=".Api::$api_key."&format=json";
            Api::$api_url = Api::$api_base_url . "%s" . Api::$api_query_args;
            Api::$api_title = "CongressGov";
            Api::$api_item_limit = 75;

            Api::postInit();
        }

        //Make an API call with the given route and options
        //Defaults to 20 items per request
        static function call($route, $required_field = null, $additional_args = null) {//, $options) {
            $url = sprintf(Api::$api_url, $route);
            if ($additional_args !== null) $url .= "&$additional_args";
            
            $json = Api::createRequest($url)->doRequest();
            \APIService\Tracker::addLog(Api::$api_key_id, $url);
            return Api::doApiCallReturn($json, $required_field, $url);
        }

        private static $lastBulkCallTotal = -1;
        static function getLastBulkCallTotal() { return Api::$lastBulkCallTotal; }
        //Make an API call with the given route and options
        //Pulls all items for this route via the pagination property
        static function call_bulk($route, $required_field = null, $itemLimit = 750, $startOffset = 0, $additionalArgs = null) {
            $full_route_json = []; $json = "";
            $url = sprintf(Api::$api_url, $route);
            //Keep track of the record offset for pagination
            $offset = $startOffset; $pageLimit = Api::$api_item_limit; $doneCalling = false; $data_array_name = null;
            if ($itemLimit < $pageLimit) $pageLimit = $itemLimit;
            //Fetch API pages while pages exist
            do {
                //print("start: $startOffset, offset: $offset, pageLimit: $pageLimit, itemLimit: $itemLimit");
                //Make the API call with offset and limit arguments appended
                if (($offset-$startOffset) + $pageLimit > $itemLimit) $pageLimit = $itemLimit - $offset;
                $args = "&offset=$offset&limit=$pageLimit&$additionalArgs";
                $nextUrl = $url . $args;
                $json = Api::createRequest($nextUrl)->doRequest();
                \APIService\Tracker::addLog(Api::$api_key_id, $nextUrl);

                //Determine which key stores the data in this response, based on expected response having [pagination, request, $data_array]
                if ($data_array_name == null) $data_array_name = array_values(array_diff(array_keys($json), ["pagination", "request"]))[0];
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
            if (isset($json["pagination"])) Api::$lastBulkCallTotal = $json["pagination"]["count"];
            else Api::$lastBulkCallTotal = count($full_route_json);
            //Remove pagination section since paging isn't nessesary for this request (anymore)
            unset($json["pagination"]);
            unset($json["request"]);

            return Api::doApiCallReturn($json, $required_field, $url);
        }
    }
    //Initialize private members
    \CongressGov\API::init();
}

?>