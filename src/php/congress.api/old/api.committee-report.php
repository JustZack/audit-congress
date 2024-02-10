<?php

function GetCommitteeReports() {
    $committee_reports = API_CALL("committee-report");
    return $committee_reports;
}
function GetCommitteeReportsByCongress($congress) {
    $committee_reports = API_CALL("committee-report/$congress");
    return $committee_reports;
}
function GetCommitteeReportsByCongressByType($congress, $reportType) {
    $committee_reports = API_CALL("committee-report/$congress/$reportType");
    return $committee_reports;
}
function GetCommitteeReport($congress, $reportType, $number) {
    $committee_report = API_CALL("committee-report/$congress/$reportType/$number");
    return $committee_report;
}
function GetCommitteeReportTexts($congress, $reportType, $number) {
    $committee_report_texts = API_CALL("committee-report/$congress/$reportType/$number/text");
    return $committee_report_texts;
}
?>