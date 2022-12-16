<?php

include_once "class.state.php";

class Term {
    public $chamber;
    public $congress;
    public $district;
    public $memberType;
    public State $state;
    public $termBeginYear;
    public $termEndYear;

    public function __construct($chamber, $congress, $district, $memberType, $stateCode, $termBeginYear, $termEndYear) {
        $this->chamber = $chamber;
        $this->congress = $congress;
        $this->district = $district;
        $this->memberType = $memberType;
        $this->state = State::GetByCode($termAPIData["stateCode"]);
        $this->termBeginYear = $termBeginYear;
        $this->termEndYear = $termEndYear;
    }

    public function BuildFromAPIData($termAPIData) {
        return new Term($termAPIData["chamber"],$termAPIData["congress"],
        $termAPIData["district"],$termAPIData["memberType"],$termAPIData["stateCode"],
        $termAPIData["termBeginYear"],$termAPIData["termEndYear"]);
    }

}

?>