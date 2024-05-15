<?php

namespace AuditCongress {

    class BillSubjectRow extends \MySqlConnector\SqlRow {
        public
            $id,
            $billId,

            $type,
            $congress,
            $number,

            $subjectIndex,

            $subject;
    
        public function getColumns() {
            return ["id","billId","type","congress","number","subjectIndex","subject"];
        }

        public function getValues() {
            return [$this->id,$this->billId,$this->type,$this->congress,$this->number,
                    $this->subjectIndex,$this->subject];
        }
    }
}

?>