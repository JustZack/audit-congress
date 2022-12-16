<?php

function GetNominations() {
    $nominations = API_CALL("nomination");
    return $nominations;
}
function GetNominationsByCongress($congress) {
    $nominations = API_CALL("nomination/$congress");
    return $nominations;
}
function GetNomination($congress, $number) {
    $nomination = API_CALL("nomination/$congress/$number");
    return $nomination;
}
function GetNominationByOrdinal($congress, $number, $ordinal) {
    $nominees = API_CALL("nomination/$congress/$number/$ordinal");
    return $nominees;
}
function GetNominationActions($congress, $number) {
    $nomination_actions = API_CALL("nomination/$congress/$number/actions");
    return $nomination_actions;
}
function GetNominationCommittees($congress, $number) {
    $nomination_committees = API_CALL("nomination/$congress/$number/committees");
    return $nomination_committees;
}
function GetNominationHearings($congress, $number) {
    $nomination_hearings = API_CALL("nomination/$congress/$number/hearings");
    return $nomination_hearings;
}
?>