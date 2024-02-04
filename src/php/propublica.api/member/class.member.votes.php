<?php

namespace ProPublica {
    require_once PROPUBLICA_FOLDER."/api/propublica.api.php";
    require_once AUDITCONGRESS_FOLDER."/abstract.api.object.php";
    class MemberVotes extends \AuditCongress\ApiObject {
        public
            $uid,
            $id,
            $member_id,
            $total_votes,
            $offset,
            $votes;

        function __construct($memberId) {
            $this->id = $memberId;

            $this->route = "members/$this->id/votes";
            $this->setUidFromRoute();
            $this->route .= ".json";
        }

        function fetchFromApi() {
            $memberVotes = Api::call($this->route, "results")[0];
            $this->setFromApi($memberVotes);
        }
    }
}

?>