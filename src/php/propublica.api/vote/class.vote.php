<?php
require_once "propublica.api.php";
require_once "interface.api.object.php";

class Vote implements ProPublicaApiObject{
    use getAndPrintAsJson;
    public
        $uid,
        $congress,
        $chamber,
        $session,
        $roll_call,

        $source,
        $url,
        $bill,
        $amendment,
        $question,
        $question_text,
        $description,

        $vote_type,
        $date,
        $time,
        $result,
        $tie_breaker,
        $tie_breaker_vote,
        $document_number,
        $document_title,
        
        $democratic,
        $republican,
        $independent,
        $total,
        $positions,
        
        $vacant_seats;


    function __construct($congressNumber, $chamber, $sessionNumber, $rollCallNumber) {
        $this->congress = $congressNumber;
        $this->chamber = $chamber;
        $this->session = $sessionNumber;
        $this->roll_call = $rollCallNumber;
    }

    function fetchFromApi() {
        $result = PROPUBLICA_API_CALL("$this->congress/$this->chamber/sessions/$this->session/votes/$this->roll_call.json");
        $mem = $result["results"]["votes"];
        $this->setFromApi($mem);
        $this->getUid();
    }

    function setFromApi($apiRes) {
        foreach ($apiRes["vote"] as $key=>$value) $this->{$key} = $value;
        $this->vacant_seats = $apiRes["vacant_seats"];
    }

    function getUid() {
        if (isset($this->uid)) return $this->uid;
        else $this->uid = "vote.$this->chamber.$this->congress.$this->session.$this->roll_call";
        return $this->uid;
    }
}

?>
