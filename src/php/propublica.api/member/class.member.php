<?php


namespace ProPublica {
    require_once "propublica.api.php";
    require_once "abstract.api.object.php";
    class Member extends ApiObject {
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
            $mem = $result["results"][0];
            $this->setFromApi($mem);
            $this->getUid();
        }

        function getUid() {
            if (isset($this->uid)) return $this->uid;
            else $this->uid = "member.$this->member_id";
            return $this->uid;
        }
    }
}

?>