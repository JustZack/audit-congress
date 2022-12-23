<?php
function GetAmendments() {
    $amendments = CONGRESS_API_CALL("amendment");
    return $amendments;
}
function GetAmendmentsByCongress($congress) {
    $amendments = CONGRESS_API_CALL("amendment/$congress");
    return $amendments;
}
function GetAmendmentsByCongressByType($congress, $type) {
    $amendments = CONGRESS_API_CALL("amendment/$congress/$type");
    return $amendments;
}
function GetAmendment($congress, $type, $number) {
    $amendment = CONGRESS_API_CALL("amendment/$congress/$type/$number");
    return $amendment;
}
function GetAmendmentActions($congress, $type, $number) {
    $amendment = CONGRESS_API_CALL("amendment/$congress/$type/$number/actions");
    return $amendment;
}
function GetAmendmentCoSponsors($congress, $type, $number) {
    $amendment = CONGRESS_API_CALL("amendment/$congress/$type/$number/cosponsors");
    return $amendment;
}
function GetAmendmentAmendments($congress, $type, $number) {
    $amendment = CONGRESS_API_CALL("amendment/$congress/$type/$number/amendments");
    return $amendment;
}

?>