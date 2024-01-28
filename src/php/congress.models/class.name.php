<?php

class Name {
    public $first;
    public $middle;
    public $last;
    public $nickname;

    public function __construct($first, $middle, $last, $nickname) {
        $this->first = $first;
        $this->middle = $middle;
        $this->last = $last;
        $this->nickname = $nickname;
    }

    public function Full() {
        if (isset($this->middle)) {
            return "$first $middle $last";
        } else {
            return "$first $last";
        }
    }

    public function DirectOrder() {
        return ucwords("$first $last");
    }

    public function InvertedOrder() {
        return ucwords("$last, $first");
    }

    public static function BuildFromAPIData($nameAPIData) {
        return new Name($nameAPIData["firstName"], $nameAPIData["middleName"], $nameAPIData["lastName"], $nameAPIData["nickName"]);
    }
}

?>