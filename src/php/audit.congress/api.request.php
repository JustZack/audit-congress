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

        public static function parseJson($toParse) {
            if ($toParse !== false) return json_decode($toParse, true);
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