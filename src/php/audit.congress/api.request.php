<?php

namespace AuditCongress {
    class ApiRequest {
        public 
            $url,
            $headers,
            $result;
        
        public function __construct($url, $headers = null) {
            $this->url = $url;
            $this->headers = $headers;
        }

        public static function parseJson($json) {
            if ($json !== false)    return json_decode($json, true);
            else                    return false;
        }

        public function doRequest() {
            if ($this->headers == null) $this->result = @file_get_contents($this->url);
            else                        $this->result = @file_get_contents($this->url, false, $this->headers);

            $this->result = ApiRequest::parseJson($this->result);
            return $this->result;
        }
    }
}

?>