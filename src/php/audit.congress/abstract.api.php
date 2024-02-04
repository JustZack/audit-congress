<?php

namespace AuditCongress {
    abstract class Api {
        //Fields every API needs
        private static $api_key,$api_item_limit = 20,$api_base_url,$api_url;

        //Define a valid response based off provided field
        static function responseIsValid($response, $required_field) {
            return isset($response) && isset($response[$required_field]);
        }

        //Throw an exception when this api fails
        static function throwApiError($api_title, $url, $type) {
            throw new ApiException($type, $url, $api_title);
        }

        //Return for no API key being set
        static function noApiKeySet($url, $api_title) {
            Api::throwApiError($api_title, $url, "nokey");
        }

        //Return for api GET function
        static function doApiGetReturn($json , $url, $api_title) {
            if ($json !== false) return json_decode($json, true);
            else Api::throwApiError($api_title, $url, "fail");
        }

        //Handle api calls with or without headers
        static function doApiGet($url, $headers = null) {
            
            if ($headers == null) return @file_get_contents($url);
            else                  return @file_get_contents($url, false, $headers);
        }

        //Return for api CALL functions
        static function doApiCallReturn($json, $required_field, $url, $api_title) {
            if (!isset($required_field))                            return $json;
            else if (Api::responseIsValid($json, $required_field))  return $json[$required_field];
            else                                                    Api::throwApiError($api_title, $url, "null");
        }

        //Initialize required API members
        static abstract function init();

        //Generic GET call using the api, varies by api implementation
        static abstract function get($url);

        //Make an api call, or make as many as pagination allows
        static abstract function call($route, $required_field = null, $additional_args = null);
        static abstract function call_bulk($route, $required_field = null, $limit = null, $additionalArgs = null);
    }

    class ApiException extends \Exception{
        public $type, $url, $api_title, $error;
        function __construct($type, $url, $api_title) {
            $this->error = ApiException::determineErrorMessage($type);
            $this->type = $type;
            $this->url = $url;
            $this->api_title = $api_title;

            $message = $this->api_title . ".Api => $this->url $this->error";
            \Exception::__construct($message);
        }

        static function determineErrorMessage($type) {
            $error = "";
            switch ($type) {
                case "null": $error = "returned null value"; break;
                case "fail": $error = "request failed"; break;
                case "nokey": $error = "no  api key set"; break;
                default: $error = "encountered an exception"; break;
            }
            return $error;
        }
    }
}

?>