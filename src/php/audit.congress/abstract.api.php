<?php

namespace AuditCongress {
    abstract class Api {
        //Fields every API needs to implement
        /*public static 
            $api_key,
            $api_item_limit,
            $api_base_url,
            $api_query_args,
            $api_url,
            $api_headers,
            $api_title;
        */

        //Define a valid response based off provided field
        static function responseIsValid($response, $required_field) {
            return isset($response) && isset($response[$required_field]);
        }

        //Throw an exception when this api fails
        static function throwApiException($api_title, $url, $type) {
            throw new ApiException($type, $url, $api_title);
        }

        //Return for no API key being set
        static function noApiKeySet($url, $api_title) {
            Api::throwApiException($api_title, $url, "nokey");
        }

        //Return for api CALL functions
        static function doApiCallReturn($json, $required_field, $url, $api_title) {
            if ($json == false)                                     Api::throwApiException($api_title, $url, "fail");
            else if (!isset($required_field))                       return $json;
            else if (Api::responseIsValid($json, $required_field))  return $json[$required_field];
            else                                                    Api::throwApiException($api_title, $url, "null");
        }

        static abstract function createRequest($url);

        //Initialize required API members
        static abstract function init();
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