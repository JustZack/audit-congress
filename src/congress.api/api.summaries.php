<?php
function GetSummaries() {
    $summaries = API_CALL("summaries");
    return $summaries;
}
function GetSummariesBycongress($congress) {
    $summaries = API_CALL("summaries/$congress");
    return $summaries;
}
function GetSummariesBycongressByType($congress, $type) {
    $summaries = API_CALL("summaries/$congress/$type");
    return $summaries;
}
?>