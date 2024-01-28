<?php

include_once "src/congress.api/congress.api.php";

class BillCommittees {
    public $billCongress;
    //One of: hr, s, hjres, sjres, hconres, sconres, hres, or sres
    public $billType;
    public $billNumber;

    public $actionCode;
    public $actionDate;
    public $sourceSystemCode;
    public $sourceSystemName;
    public $text;
    public $type;

    public function __construct($congressNumber, $billType, $billNumber) {
        $this->billCongress = $congressNumber;
        $this->billType = $billType;
        $this->billNumber = $billNumber;
    } 

    //Load the bill data based on whats in the cache
    /* To be replaced by loadFromDB */
    public function loadFromCache() {

    }

    //Load the bill data based on whats in the database
    /* to use SQL database as cache */
    public function loadFromDB() {

    }

    //Load bill data from API endpoints
    /* Pull bill data from the source*/
    public function loadFromAPI() {

    }

    //Load bill data based on available sources
    public function load() {

    }

    public static function BuildFromAPIData($billData) {

    }
}

?>