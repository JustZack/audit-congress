<?php

class MemberVote {
    use getAndPrintAsJson;
    public
        $uid,
        $member_id,
        $chamber,
        $congress,
        $session,
        $roll_call,
        $vote_uri,
        $description,
        $question,
        $result,
        $date,
        $time,
        $position;

    function __construct($apiData) {
        $this->setFromApi($apiData);
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

    function createFromSet($set) {
        foreach ($set as $key=>$value) {

        }
    }
}

?>