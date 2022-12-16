<?php

function GetTreaties() {
    $treaties = API_CALL("treaty");
    return $treaties;
}
function GetTreatiesByCongress($congress) {
    $treaties = API_CALL("treaty/$congress");
    return $treaties;
}
function GetTreaty($congress, $number) {
    $treaty = API_CALL("treaty/$congress/$number");
    return $treaty;
}
function GetTreatyActions($congress, $number) {
    $actions = API_CALL("treaty/$congress/$number/actions");
    return $actions;
}
function GetPartitionedTreaty($congress, $number, $suffix) {
    $treaty = API_CALL("treaty/$congress/$number/$suffix");
    return $treaty;
}
function GetPartitionedTreatyActions($congress, $number, $suffix) {
    $treaty = API_CALL("treaty/$congress/$number/$suffix/actions");
    return $treaty;
}
function GetTreatyCommittees($congress, $number) {
    $committees = API_CALL("treaty/$congress/$number/committees");
    return $committees;
}

?>