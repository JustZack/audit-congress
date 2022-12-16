<?php

class Party {
    public $code;
    public $name;

    public function __construct($code, $name) {
        $this->code = $code;
        $this->name = $name;
    }

    public function GetByCode($code) {
        if (array_key_exists($code, Party::$codeMapping)) {
            $name = Party::$codeMapping[$code];
            return new Party($code, $name);
        } else {
            return new Party($code, "UNKNOWN");
        }
    }
    public function GetByName($name) {
        $code = array_search($name, Party::$codeMapping);
        if ($code !== false) {
            return new Party($code, $name);
        } else {
            return new Party("UNKNOWN", $name);
        }
    }

    private static $codeMapping = array('D'=>"Democrat", 'R'=>"Republican");  
}

?>