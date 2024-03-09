<?php


namespace ProPublica {
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

            $this->route = "members/$this->id";
            $this->setUidFromRoute();
            $this->route .= ".json";

            $this->fetchFromApi();
        }

        function fetchFromApi() {
            $member = Api::call($this->route, "results")[0];
            $this->setFromApi($member);
        }
    }
}

?>