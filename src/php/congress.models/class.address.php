<?php

class Address {
    public $city;
    public $district;
    public $officeAddress;
    public $officeTelephone;
    public $zipCode;

    public function __construct($city, $district, $officeAddress, $officeTelephone, $zipCode) {
        $this->city = $city;
        $this->district = $district;
        $this->officeAddress = $officeAddress;
        $this->officeTelephone = $officeTelephone;
        $this->zipCode = $zipCode;
    }

    public static function BuildFromAPIData($addressAPIData) {
        return new Address($addressAPIData["city"], $addressAPIData["district"],
        $addressAPIData["officeAddress"], $addressAPIData["officeTelephone"]["phoneNumber"],
        $addressAPIData["zipCode"]);
    }
}

?>