<?php

class PartyHistory {
    public $startYear;
    public $endYear;
    public Party $party;

    public function __construct($startYear, $endYear, $partyCode) {
        $this->startYear = $startYear;
        $this->endYear = $endYear;
        $this->party = Party::GetByCode($partyCode);
    }

    public static function BuildFromAPIData($partyHistoryAPIData) {
        return new PartyHistory($partyHistoryAPIData["startYear"], $partyHistoryAPIData["endYear"], $partyHistoryAPIData["partyCode"]);
    }
}

?>