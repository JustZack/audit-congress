<?php

namespace AuditCongress {

    class BillTitleRow extends \MySqlConnector\SqlRow {
        public
            $id,
            $billId,

            $type,
            $congress,
            $number,

            $index,

            $title,
            $titleAs,
            $titleType,
            $isForPortion;
    
        public function getColumns() {
            return ["id","billId","type","congress","number","index","title","titleAs","titleType", "isForPortion"];
        }

        public function getValues() {
            return [$this->id,$this->billId,$this->type,$this->congress,$this->number,
            $this->index,$this->title,$this->titleAs,$this->titleType,$this->isForPortion];
        }
    }
}

?>