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
        }

        function fetchFromApi() {
            $result = Api::call("members/$this->id/votes.json");
            if (isset($result) && isset($result["results"]) && isset($result["results"][0])) {
                $mem = $result["results"][0];
                $this->setFromApi($mem);
                $this->getUid();
            } else throw new \Exception("ProPublica.Api => members/$this->id/votes.json returned null value");
        }

        function getUid() {
            if (isset($this->uid)) return $this->uid;
            else $this->uid = "member.$this->member_id.votes";
            return $this->uid;
        }
    }
}

?>