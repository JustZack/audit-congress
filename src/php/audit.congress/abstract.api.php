<?php

namespace AuditCongress {
    abstract class Api {
        //Fields every API needs
        private static $api_key,$api_item_limit = 20,$api_base_url,$api_url;

        //Initialize required API members
        static abstract function init();

        //Generic GET call using the api
        static abstract function get($url);
        //Make a call
        static abstract function call($route, $additional_args = null);
        static abstract function call_bulk($route, $options, $additional_args = null);
    }
}

?>