<?php

class APIRouteValidator {
     //Check if arguments match bill option call
     public static function shouldFetchBillOption($congress, $type, $number, $option) {
        return isset($congress) && isset($type) && isset($number) && isset($option);
    }
    //Check if arguments match bill call
    public static function shouldFetchBill($congress, $type, $number, $option) {
        return isset($congress) && isset($type) && isset($number) && !isset($option);
    }
    //Check if arguments match bills by congress by type call
    public static function shouldFetchBillsByCongressByType($congress, $type, $number, $option) {
        return isset($congress) && isset($type) && !isset($number) && !isset($option);
    }
    //Check if arguments match bills by congress call
    public static function shouldFetchBillsByCongress($congress, $type, $number, $option) {
        return isset($congress) && !isset($type) && !isset($number) && !isset($option);
    }

    //Check if arguments match recent bills call
    public static function shouldFetchRecentBillsPage($page) {
        return isset($page);
    }
}

?>