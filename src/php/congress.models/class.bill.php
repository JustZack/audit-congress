<?php

include_once "src/congress.api/congress.api.php";
include_once "class.bill.action.php";
include_once "class.bill.amendment.php";

class Bill {
    public $congress;
    //One of: hr, s, hjres, sjres, hconres, sconres, hres, or sres
    public $type;
    public $number;
    public $authorityStatement;
    public $introducedDate;
    public $originChamber;
    public $title;
    public $updateDate;
    public $updateDateIncludingText;

    //Array of BillAction
    public $actions = array();

    //Array of BillAmendment
    public $amendments = array();

    public function __construct($congressNumber, $billType, $billNumber) {
        $this->congress = $congressNumber;
        $this->type = $billType;
        $this->number = $billNumber;
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