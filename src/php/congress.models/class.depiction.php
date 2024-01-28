<?php

class Depiction {
    public $attribution;
    public $imageUrl;

    public function __construct($imageUrl, $attribution) {
        $this->imageUrl = $imageUrl;
        $this->attribution = $attribution;
    }
    public static function BuildFromAPIData($depictionAPIData) {
        return new Depiction($depictionAPIData["imageUrl"], $depictionAPIData["attribution"]);
    }


}

?>