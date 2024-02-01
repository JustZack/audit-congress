<?php

namespace ProPublica {
    require_once "propublica.api.php";
    require_once "../audit.congress/abstract.api.object.php";
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
        }

        function fetchFromApi() {
            $result = Api::call("members/$this->id/votes.json");
            $mem = $result["results"][0];
            $this->setFromApi($mem);
            $this->getUid();
        }

        function getUid() {
            if (isset($this->uid)) return $this->uid;
            else $this->uid = "member.$this->member_id.votes";
            return $this->uid;
        }
    }
}

?>