<?php 

namespace AuditCongress {

    class MemberTerm extends \MySqlConnector\SqlRow {
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
            $phone;

            public function getColumns() {
                return ["bioguideId"];
            }
        
            public function getValues() {
                return [$this->bioguideId];
            }

    }
}

?>