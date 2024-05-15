<?php

namespace API {
    class TitlesByFilter extends TitlesRoute {

        //Note: No required parameters        
        public static function fetchResult() {
            $congress = Parameters::getInt("congress");
            $type = Parameters::get("type");
            $number = Parameters::getInt("number");
            $title = Parameters::get("title");

            return \AuditCongress\BillTitles::getByFilter($congress, $type, $number, $title);
        }
    }
}

?>