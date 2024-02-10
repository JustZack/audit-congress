<?php

class APIRouteValidator {
     //Check if arguments match bill option call
     public static function shouldFetchBillOption($congress, $type, $number, $option) {
        return isset($congress) && isset($type) && isset($number) && isset($option) &&
               is_numeric($congress) && is_string($type) && is_numeric($number) && is_string($option);
    }
    //Check if arguments match bill call
    public static function shouldFetchBill($congress, $type, $number, $option) {
        return isset($congress) && isset($type) && isset($number) && !isset($option) &&
               is_numeric($congress) && is_string($type) && is_numeric($number);
    }
    //Check if we could fetch a bill or and option
    public static function couldFetchBillOrOption($congress, $type, $number, $option) {
        return self::shouldFetchBill($congress, $type, $number, $option) 
            || self::shouldFetchBillOption($congress, $type, $number, $option);
    }
    //
    public static function shouldFetchFullBill($congress, $type, $number, $option) {
        return isset($congress) && isset($type) && isset($number) && !isset($option) &&
               is_numeric($congress) && is_string($type) && is_numeric($number);
    }
    //Check if arguments match bills by congress by type call
    public static function shouldFetchBillsByCongressByType($congress, $type, $number, $option) {
        return isset($congress) && isset($type) && !isset($number) && !isset($option) &&
               is_numeric($congress) && is_string($type);
    }
    //Check if arguments match bills by congress call
    public static function shouldFetchBillsByCongress($congress, $type, $number, $option) {
        return isset($congress) && !isset($type) && !isset($number) && !isset($option) &&
               is_numeric($congress);
    }

    //Check if arguments match recent bills call
    public static function shouldFetchRecentBillsPage($page) {
        return isset($page) && is_numeric($page);
    }

    //Check if arguments match members listing call
    public static function shouldFetchMembersList($member) {
        return !isset($member);
    }
    //Check if arguments match member call
    public static function shouldFetchMember($member) {
        return isset($member);
    }
    //Check if arguments match full member call
    public static function shouldFetchFullMember($member, $option) {
        return isset($member) && !isset($option);
    }
    //Check if arguments match member option call
    public static function shouldFetchMemberOption($member, $option) {
        return isset($member) && isset($option);
    }
    //Check if we could fetch a member or and option
    public static function couldFetchMemberOrOption($member, $option) {
        return self::shouldFetchMember($member) 
            || self::shouldFetchMemberOption($member, $option);
    }
}

?>