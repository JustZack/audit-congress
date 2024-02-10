<?php

//House Communications endpoints
function GetHouseCommunications() {
    $house_communications = API_CALL("house-communication");
    return $house_communications;
}
function GetHouseCommunicationsByCongress($congress) {
    $house_communications = API_CALL("house-communication/$congress");
    return $house_communications;
}
function GetHouseCommunicationsByCongressByType($congress, $type) {
    $house_communications = API_CALL("house-communication/$congress/$type");
    return $house_communications;
}
function GetHouseCommunication($congress, $type, $number) {
    $house_communication = API_CALL("house-communication/$congress/$type/$number");
    return $house_communication;
}

//Senate Communications endpoints
function GetSenateCommunications() {
    $senate_communications = API_CALL("senate-communication");
    return $senate_communications;
}
function GetSenateCommunicationsByCongress($congress) {
    $senate_communications = API_CALL("senate-communication/$congress");
    return $senate_communications;
}
function GetSenateCommunicationsByCongressByType($congress, $type) {
    $senate_communications = API_CALL("senate-communication/$congress/$type");
    return $senate_communications;
}
function GetSenateCommunication($congress, $type, $number) {
    $senate_communication = API_CALL("senate-communication/$congress/$type/$number");
    return $senate_communication;
}

?>