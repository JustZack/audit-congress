<?php

include_once "class.name.php";
include_once "class.state.php";
include_once "class.depiction.php";
include_once "class.address.php";

class Member {
    public $id;
    public $district;
    public Party $party;
    public Name $name;
    public State $state;
    public Depiction $depiction;
    public Address $address;
    public $website;

    public array $partyHistory;
    public array $termHistory;

    public $isCurrentMember;
    public $birthYear;
    public $deathYear;

    public $isBrief;

    public function __construct($memberData) {
        //Full member data
        //(Only when using the /member/ route)
        if (isset($memberData["identifiers"])) {
            $this->id = $memberData["identifiers"]["bioguideId"];
            $this->district = $memberData["district"];
            $this->party = Party::GetByName($memberData["party"]);
            $this->name = Name::BuildFromAPIData($memberData);
            $this->state = State::GetByName($memberData["state"]);
            $this->depiction = Depiction::BuildFromAPIData($memberData["depiction"]);
            $this->address = Address::BuildFromAPIData($memberData["addressInformation"]);
            $this->website = $memberData["officialWebsiteUrl"];

            $this->partyHistory = array();
            foreach ($memberData["partyHistory"] as $history)
                $this->partyHistory[] = PartyHistory::BuildFromAPIData($history);

            $this->termHistory = array();
            foreach ($memberData["terms"] as $term)
                $this->termHistory[] = Term::BuildFromAPIData($term);

            $this->currentMember = $memberData["currentMember"];
            $this->birthYear = $memberData["birthYear"];
            $this->deathYear = $memberData["deathYear"];

            $this->isBrief = false;
        }
        //Brief member data
        //(When member is included with other data, like a /bill/)
        else {
            $this->id = $memberData["bioguideId"];
            $this->district = $memberData["district"];
            $this->party = Party::GetByCode($memberData["party"]);
            $this->name = Name::BuildFromAPIData($memberData);
            $this->state = State::GetByCode($memberData["state"]);

            $this->isBrief = true;
        }
    } 

    public function GetRepName() {
        $invertedName = $this->name->InvertedOrder();
        $location = $this->party->code."-".$this->state->code;
        if (strlen($this->district) > 0) $location .= "-$this->district";

        return "Rep. $invertedName [$location]";
    }
}

?>