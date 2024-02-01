<?php


namespace ProPublica {
    require_once PROPUBLICA_FOLDER."/api/propublica.api.php";
    require_once AUDITCONGRESS_FOLDER."/abstract.api.object.php";
    class Member extends \AuditCongress\ApiObject {
        public
            $uid,

            $id,
            $member_id,

            $first_name,
            $middle_name,
            $last_name,
            $suffix,

            $in_office,
            $current_party,
            $date_of_birth,
            $gender,

            $url,
            $times_topics_url,
            $times_tag,

            $govtrack_id,
            $cspan_id,
            $votesmart_id,
            $icpsr_id,
            $crp_id,
            $google_entity_id,

            $twitter_account,
            $facebook_account,
            $youtube_account,
            
            $rss_url,
            $most_recent_vote,
            $last_updated,
            $roles;

        function __construct($memberId) {
            $this->id = $memberId;
        }

        function fetchFromApi() {
            $result = Api::call("members/$this->id.json");
            if (isset($result) && isset($result["results"]) && isset($result["results"][0])) {
                $mem = $result["results"][0];
                $this->setFromApi($mem);
                $this->getUid();
            } else throw new \Exception("ProPublica.Api => members/$this->id.json returned null value");
        }

        function getUid() {
            if (isset($this->uid)) return $this->uid;
            else $this->uid = "member.$this->member_id";
            return $this->uid;
        }
    }
}

?>