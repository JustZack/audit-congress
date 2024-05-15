<?php

namespace API {
    class SubjectsByFilter extends SubjectsRoute {

        //Note: No required parameters        
        public static function fetchResult() {
            $congress = Parameters::getInt("congress");
            $type = Parameters::get("type");
            $number = Parameters::getInt("number");
            $subject = Parameters::get("subject");

            return \AuditCongress\BillSubjects::getByFilter($congress, $type, $number, $subject);
        }
    }
}

?>