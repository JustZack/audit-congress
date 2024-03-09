<?php

namespace AuditCongress {
   
    class MemberTermRow extends \MySqlConnector\SqlRow {
        public
            $bioguideId,
            $type,
            $start,
            $end,
            $state,
            $district,
            $party,
            $class,
            $how,

            $state_rank,
            $url,
            $rss_url,
            $contact_form,
            $address,
            $office,
            $phone,

            $lastUpdate,
            $nextUpdate;

        public function getColumns() {
            return ["bioguideId","type","start","end",
            "state","district","party","class","how","state_rank",
            "url","rss_url","contact_form","address","office","phone",
            "lastUpdate","nextUpdate"];
        }
    
        public function getValues() {
            return [$this->bioguideId,$this->type,$this->start,$this->end,
            $this->state,$this->district,$this->party,$this->class,
            $this->how,$this->state_rank,$this->url,$this->rss_url,
            $this->contact_form,$this->address,$this->office,$this->phone,
            $this->lastUpdate,$this->nextUpdate];
        }
    }
}

?>