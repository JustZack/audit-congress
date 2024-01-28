<?php

class State {
    public $code;
    public $name;

    public function __construct($code, $name) {
        $this->code = $code;
        $this->name = $name;
    }

    public function GetByCode($code) {
        if (array_key_exists($code, State::$codeMapping)) {
            $name = State::$codeMapping[$code];
            return new State($code, $name);
        } else {
            return new State($code, "UNKNOWN");
        }
    }
    public function GetByName($name) {
        $code = array_search($name, State::$codeMapping);
        if ($code !== false) {
            return new State($code, $name);
        } else {
            return new State("UNKNOWN", $name);
        }
    }

    private static $codeMapping = array('AL'=>"Alabama",  
    'AK'=>"Alaska",  
    'AZ'=>"Arizona",  
    'AR'=>"Arkansas",  
    'CA'=>"California",  
    'CO'=>"Colorado",  
    'CT'=>"Connecticut",  
    'DE'=>"Delaware",  
    'DC'=>"District Of Columbia",  
    'FL'=>"Florida",  
    'GA'=>"Georgia",  
    'HI'=>"Hawaii",  
    'ID'=>"Idaho",  
    'IL'=>"Illinois",  
    'IN'=>"Indiana",  
    'IA'=>"Iowa",  
    'KS'=>"Kansas",  
    'KY'=>"Kentucky",  
    'LA'=>"Louisiana",  
    'ME'=>"Maine",  
    'MD'=>"Maryland",  
    'MA'=>"Massachusetts",  
    'MI'=>"Michigan",  
    'MN'=>"Minnesota",  
    'MS'=>"Mississippi",  
    'MO'=>"Missouri",  
    'MT'=>"Montana",
    'NE'=>"Nebraska",
    'NV'=>"Nevada",
    'NH'=>"New Hampshire",
    'NJ'=>"New Jersey",
    'NM'=>"New Mexico",
    'NY'=>"New York",
    'NC'=>"North Carolina",
    'ND'=>"North Dakota",
    'OH'=>"Ohio",  
    'OK'=>"Oklahoma",  
    'OR'=>"Oregon",  
    'PA'=>"Pennsylvania",  
    'RI'=>"Rhode Island",  
    'SC'=>"South Carolina",  
    'SD'=>"South Dakota",
    'TN'=>"Tennessee",  
    'TX'=>"Texas",  
    'UT'=>"Utah",  
    'VT'=>"Vermont",  
    'VA'=>"Virginia",  
    'WA'=>"Washington",  
    'WV'=>"West Virginia",  
    'WI'=>"Wisconsin",  
    'WY'=>"Wyoming");
}

?>