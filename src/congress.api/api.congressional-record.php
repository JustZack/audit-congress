<?php

function GetCongressionalRecords() {
    $congressional_records = API_CALL("congressional-record");
    return $congressional_records;
}

?>