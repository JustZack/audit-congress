<?php 

/*
    Queries endpoints found on:
        https://projects.propublica.org/api-docs/congress-api/
*/

namespace UnitedStatesLegislators {
    require_once AUDITCONGRESS_FOLDER."/abstract.api.php";
    require_once AUDITCONGRESS_FOLDER."/api.request.php";
    class Api extends \AuditCongress\Api {
        public static 
            $api_key,
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
            Api::$api_base_url = "https://theunitedstates.io/congress-legislators/";
            Api::$api_key = "none";
            Api::$api_url = Api::$api_base_url . "%s";
            Api::$api_title = "UnitedStates.Legislators";
            Api::$api_item_limit = 20;

            Api::postInit();
        }

        //Make an API call with the given route and options
        //Defaults to 20 items per request
        static function call($route, $required_field = null, $additional_args = null) {//, $options) {
            $url = sprintf(Api::$api_url, $route);            
            $json = Api::createRequest($url)->doRequest();
            return Api::doApiCallReturn($json, $required_field, $url, Api::$api_title);
        }

        //Make an API call with the given route and options
        //Pulls all items for this route via the pagination property
        static function call_bulk($route, $required_field = null, $limit = 750, $additionalArgs = null) {
            $url = sprintf(Api::$api_url, $route);
            Api::notImplemented($url);
        }
    }
    //Initialize private members
    \UnitedStatesLegislators\API::init();
}

?>
