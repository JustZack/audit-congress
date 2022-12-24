<?php

class CongressAPITranslator {    
    private static function pluralizeNumber($number) {
        $relevent = $number%100;
        $tens = $relevent%10;
        $suffix = "";
    
        if ($tens == 0 || ($relevent >= 4 && $relevent <= 20)) 
                            $suffix = "th";
        else if ($tens == 1) $suffix = "st";
        else if ($tens == 2) $suffix = "nd";
        else if ($tens == 3) $suffix = "rd";
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
        $bill = $data["bill"];
        
        $data["bill"] = $bill;
        return $data;
    }
}

?>