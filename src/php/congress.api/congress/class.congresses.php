<?php


namespace CongressGov {
    class Congresses extends \AuditCongress\ApiObject {
        public
            $uid,

            $limit = 250,
            $offset = 0,
            $searchTotal = -1,

            $congresses,

            $apiDataField = "congresses",
            $objectArrayField = "congresses",
            $objectArrayType = "CongressGov\CongressListItem";

        function __construct($itemLimit = 250, $offset = 0) {
            $this->limit = $itemLimit;
            $this->offset = $offset;

            $this->route = "congress";
            $this->setUidFromRoute();

            $this->fetchFromApi();
        }

        function fetchFromApi() {
            $list = Api::call_bulk($this->route, $this->objectArrayField, $this->limit, $this->offset);
            $this->setFromApiAsArray($list, $this->objectArrayField, $this->objectArrayType);
            $this->searchTotal = Api::getLastBulkCallTotal();
        }
    }

    class CongressListItem extends \AuditCongress\ApiChildObject {
        public
            $name,
            $startYear,
            $endYear,
            $sessions,
            $number,
            $url;

            function __construct($congressListItem) {
                $this->setFieldsFromObject($congressListItem);
                $this->number = self::getCongressNumberFromUrl($this->url);
                $this->unsetField("url");
            }

            private static function getCongressNumberFromUrl($url) {
                $lastSlash = strrpos($url, "/")+1;
                $firstQuestionMark = strpos($url, "?");
                return substr($url, $lastSlash, $firstQuestionMark-$lastSlash);
            }
    }
}

?>
