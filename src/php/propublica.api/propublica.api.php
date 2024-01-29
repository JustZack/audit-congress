<?php 

/*
    Represents endpoints found on:
        https://projects.propublica.org/api-docs/congress-api/
*/


$api_key = file_get_contents("api.propublica.key");
$api_headers = stream_context_create(["http" => ["method" => "GET", "header" => "X-API-Key: $api_key\r\n"]]);
$api_item_limit = 20;
$api_base_url = "https://api.propublica.org/congress/v1/";
$api_url = $api_base_url . "%s";

//Fetch and parse JSON API data
//The base level of all API functions
function PROPUBLICA_API_GET($url) {
    global $api_key;
    global $api_headers;
    if (!$api_key || !$api_headers) print_r("Error: API Key not set");

    $json = @file_get_contents($url, false, $api_headers);
    if ($json === false) throw new Exception("Request failed: $url");

    return json_decode($json, true);
}
//Make an API call with the given route and options
//Defaults to 20 items per request
function PROPUBLICA_API_CALL($route, $additional_args = null) {//, $options) {
    global $api_url;
    $url = sprintf($api_url, "$route");
    if ($additional_args !== null) $url .= "&$additional_args";
    $json = PROPUBLICA_API_GET($url);
    return $json;
}
//Make an API call with the given route and options
//Pulls all items for this route via the pagination property
function PROPUBLICA_API_CALL_BULK($route, $options, $additional_args = null) {
    global $api_url, $api_item_limit;
    $full_route_json = []; 
    //$json; $data_array_name;
    $url = sprintf($api_url, "$route/$options");
    if ($additional_args !== null) $url .= "&$additional_args";
    //Keep track of the record offset for pagination
    $offset = 0; $doneCalling = false;
    //Fetch API pages while pages exist
    do {
        //Make the API call with offset and limit arguments appended
        $args = "?offset=$offset";
        $json = PROPUBLICA_API_GET($url . $args);
          //If this is the first run
         //Determine which key stores the data in this response
        //Based on all requests having at most [pagination, request, $data_array]
        if ($offset == 0) $data_array_name = array_values(array_diff(array_keys($json), ["pagination", "request"]))[0];
        //Increment offset by itme limit amount
        $offset += $api_item_limit;
        //No pagination property => no additional pages to fetch
        if (!isset($json["pagination"])) $doneCalling = true;
         //Fewer items left then the next item offset
        // => No more pages exist
        else if ($json["pagination"]["count"] < $offset) $doneCalling = true; 
        //Merge existing route data with new data
        $full_route_json = array_merge($full_route_json, $json[$data_array_name]);
    } while (!$doneCalling);

    //Store the full dataset in the json response
    $json[$data_array_name] = $full_route_json;
    //Remove pagination section since paging isn't nessesary for this request (anymore)
    unset($json["pagination"]);

    return $json;
}

?>