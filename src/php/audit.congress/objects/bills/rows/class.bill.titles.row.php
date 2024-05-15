<?php

namespace AuditCongress {

    class BillTitleRow extends \MySqlConnector\SqlRow {
        public
            $id,
            $billId,

            $type,
            $congress,
            $number,

            $titleIndex,

            $title,
            $titleAs,
            $titleType,
            $isForPortion;
    
        public function getColumns() {
            return ["id","billId","type","congress","number","titleIndex","title","titleAs","titleType", "isForPortion"];
        }

        public function getValues() {
            return [$this->id,$this->billId,$this->type,$this->congress,$this->number,
            $this->titleIndex,$this->title,$this->titleAs,$this->titleType,$this->isForPortion];
        }
    }
}

?>