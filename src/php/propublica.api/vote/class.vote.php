<?php
namespace ProPublica {
    class Vote extends \AuditCongress\ApiObject {
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

            $this->route = "$this->congress/$this->chamber/sessions/$this->session/votes/$this->roll_call";
            $this->setUidFromRoute();
            $this->route .= ".json";
        }

        function fetchFromApi() {
            $memberVotes = Api::call($this->route, "results")["votes"];
            $this->setFromApi($memberVotes["vote"]);
            $this->vacant_seats = $memberVotes["vacant_seats"];
        }
    }
}

?>
