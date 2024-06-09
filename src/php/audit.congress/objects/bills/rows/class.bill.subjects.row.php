<?php

namespace AuditCongress {

    class BillSubjectRow extends \MySqlConnector\SqlRow {
        public
            $id,
            $billId,

            $type,
            $congress,
            $number,

            $index,

            $subject;
    
        public function getColumns() {
            return ["id","billId","type","congress","number","index","subject"];
        }

        public function getValues() {
            return [$this->id,$this->billId,$this->type,$this->congress,$this->number,
                    $this->index,$this->subject];
        }
    }
}

?>