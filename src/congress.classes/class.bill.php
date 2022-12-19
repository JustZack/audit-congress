<?php

include_once "src/congress.api/congress.api.php";

class Bill {
    public $congress;
    public $type;
    public $number;
    public $authorityStatement;
    public $introducedDate;
    public $originChamber;
    public $updateDate;
    public $title;
    public function __construct($billData) {
        
    } 

    public static function BuildFromAPIData($billData) {

    }
}

?>