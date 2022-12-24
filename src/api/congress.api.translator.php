<?php

class CongressAPITranslator { 
    private static $routeTranslationFunctions = [
        "bill" => "CongressAPITranslator::translateBill",
        "recent.bills" => "CongressAPITranslator::translateRecentBills",
        "member" => false,
    ];
    public static function determineTranslateFunction($route) {
        $function = false;
        $knownMapping = array_key_exists($route, CongressAPITranslator::$routeTranslationFunctions);
        if ($route && $knownMapping) $function = CongressAPITranslator::$routeTranslationFunctions[$route];
        return $function;

    }

    private static $pluralizeMapping = [
        0 => "th",
        1 => "st",
        2 => "nd",
        3 => "rd"
    ];
    private static function pluralizeNumber($number) {
        $relevent = $number%100; $tens = $relevent%10;
        $suffix = ""; $isTeens = $relevent >= 4 && $relevent <= 20;

        if ($tens == 0 || $isTeens) CongressAPITranslator::$pluralizeMapping[0];
        else                        CongressAPITranslator::$pluralizeMapping[$tens];

        $suffix = $number.$suffix;
        return $suffix;
    }
    private static function getYearsByCongress($congress) {
        $n2 = $congress*2;
        //congress# * 2 + 1787 = first year of this congress session
        $years = "(".($n2+1787)." - ";
        //Then compute the second year
        //This differs because the 72nd congress sessions were 3 years, now they are 2
        if ($n2 > 72) $years .= ($n2+1788).")";
        else $years .= ($n2+1789).")";
    
        return $years;
    }
    private static function getCongressTitle($congress) {
        return CongressAPITranslator::pluralizeNumber($congress)." congress ".CongressAPITranslator::getYearsByCongress($congress);
    }
    private static function getBillID($type, $congress) {
        $billID = ""; $type = str_split($type);
        foreach ($type as $c) $billID .= $c.".";
        $billID .= $congress;
        return $billID;
    }
    private static function translateRecentBill($bill) {
        $bill["id"] = CongressAPITranslator::getBillID($bill["type"], $bill["number"]);
        $bill["congressTitle"] = CongressAPITranslator::getCongressTitle($bill["congress"]);
        return $bill;
    }
    public static function translateRecentBills($data) {
        $bills = $data["bills"];
        for ($i = 0;$i < count($bills);$i++) {
            $bills[$i] = CongressAPITranslator::translateRecentBill($bills[$i]);
        }
        $data["bills"] = $bills;
        return $data;
    }
    public static function translateBill($data) {
        if (isset($data["bill"])) {
            $bill = $data["bill"];

            $data["bill"] = $bill;
        } else {
            $option = array_values(array_diff(array_keys($data), ["pagination", "request"]))[0];
            $optionData = $data[$option];
            /*switch($option) {
                case "actions":  break;
            }*/
            $data[$option] = $optionData;
        }


        return $data;
    }
}

?>