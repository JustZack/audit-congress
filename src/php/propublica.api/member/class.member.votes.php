<?php

class MemberVotes {
    use getAndPrintAsJson;
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
        $result = PROPUBLICA_API_CALL("members/$this->id/votes.json");
        $mem = $result["results"][0];
        $this->setFromApi($mem);
        $this->getUid();
    }

    function setFromApi($apiRes) {
        foreach ($apiRes as $key=>$value) {
            $this->{$key} = $value;
        }
    }

    function getUid() {
        if (isset($this->uid)) return $this->uid;
        else $this->uid = "member.$this->member_id.votes";
        return $this->uid;
    }
}

?>