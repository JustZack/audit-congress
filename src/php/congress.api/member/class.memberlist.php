<?php


namespace CongressGov {
    class MemberList extends \AuditCongress\ApiObject {

        public
            $uid,

            $members,

            $apiDataField = "members",
            $objectArrayField = "members",
            $objectArrayType = "CongressGov\MemberListItem",

            $sortArg,
            $limit,
            $offset,
            $searchTotal = -1;

        function __construct($itemLimit = 10, $offset = 0) {
            $this->limit = $itemLimit;
            $this->offset = $offset;

            $this->route = "member/list/$offset/$itemLimit/";
            $this->setUidFromRoute();
            
            $this->route = "member/";
        }

        function fetchFromApi() {
            $list = Api::call_bulk($this->route, $this->objectArrayField, $this->limit, $this->offset, $this->sortArg);
            $this->setFromApiAsArray($list, $this->objectArrayField, $this->objectArrayType);
            $this->searchTotal = Api::getLastBulkCallTotal();
        }
    }

    class MemberListItem extends \AuditCongress\ApiChildObject {
        public
            $bioguideId,

            $depiction,

            $district,
            $name,
            $partyName,
            $state,
            $terms,
            
            $updateDate;

            function __construct($memberListItemObject) {
                $this->setFieldsFromObject($memberListItemObject);
                $this->unsetField("url");
            }
    }
}

?>