<?php


namespace CongressGov {
    class BillList extends \AuditCongress\ApiObject {
        private static 

            //sort=updateDate+[asc OR desc];
            $newestFist = "sort=updateDate+desc",
            $oldestFist = "sort=updateDate+asc";

        public
            $uid,

            $congress,
            $type,

            $bills,

            $apiDataField = "bills",
            $objectArrayField = "bills",
            $objectArrayType = "CongressGov\BillListItem",

            $sortArg, 
            $limit,
            $searchTotal = -1;

        function __construct($congress = null, $type = null, $itemLimit = 10) {
            $this->congress = $congress;
            $this->type = strlen($type) > 0 ? strtolower($type) : null;
            
            $this->limit = $itemLimit;
            
            $this->sortByNewestFirst();

            $this->route = "bill/";
            if (isset($this->congress)) {
                $this->route .= "$this->congress/";
                if (isset($this->type)) $this->route .= "$this->type/";
            }
            
            $this->setUidFromRoute();
        }

        function sortByNewestFirst() { $this->sortArg = BillList::$newestFist; }
        function sortByOldestFirst() { $this->sortArg = BillList::$oldestFist; }
 
        function fetchFromApi() {
            $list = Api::call_bulk($this->route, $this->objectArrayField, $this->limit, $this->sortArg);
            $this->setFromApiAsArray($list, $this->objectArrayField, $this->objectArrayType);
            $this->searchTotal = Api::getLastBulkCallTotal();
        }



        static function getByNewestFirst($congress = null, $type = null, $limit = 10) {
            $list = new BillList($congress, $type, $limit);
            $list->sortByNewestFirst(); return $list;
        }

        static function getByOldestFirst($congress = null, $type = null, $limit = 10) {
            $list = new BillList($congress, $type, $limit);
            $list->sortByOldestFirst(); return $list;
        }
    }

    class BillListItem extends \AuditCongress\ApiChildObject {
        public
            $congress,
            $type,
            $number,

            $originChamber,
            $originChamberCode,

            $title,
            $updateDate,
            $updateDateIncludingText;

            function __construct($billListItemObject) {
                $this->setFieldsFromObject($billListItemObject);
                $this->lowerCaseField("type");
                $this->unsetField("url");
            }
    }
}

?>