<?php
namespace ProPublica {
    require_once PROPUBLICA_FOLDER."/api/propublica.api.php";
    require_once AUDITCONGRESS_FOLDER."/abstract.api.object.php";
    class Bill extends \AuditCongress\ApiObject {
        public 
            $uid,

            $bill_id,
            $bill_slug,

            $congress,
            $number,
            $bill,
            $bill_type,
            $bill_uri,
            $title,
            $short_title,
            $sponsor_title,

            $sponsor,
            $sponsor_id,
            $sponsor_uri,
            $sponsor_party,
            $sponsor_state,

            $gpo_pdf_uri,
            $congressdotgov_url,
            $govtrack_url,
            $cbo_estimate_url,
            
            $introduced_date,
            $active,

            $last_vote,
            $house_passage,
            $senate_passage,
            $house_passage_vote,
            $senate_passage_vote,

            $enacted,
            $vetoed,

            $cosponsors,
            $withdrawn_cosponsors,
            
            $primary_subject,
            
            $latest_major_action_date,
            $latest_major_action,
            
            $summary,
            $summary_short,


            $cosponsors_by_party,
            $committees,
            $committee_codes,
            $subcommittee_codes,
            $versions,
            $actions,
            $votes,
            $presidential_statements;

        function __construct($congressNumber, $billSlug) {
            $this->congress = $congressNumber;
            $this->bill_slug = $billSlug;

            $this->route = "$this->congress/bills/$this->bill_slug";
            $this->setUidFromRoute();
            $this->route .= ".json";
        }

        function fetchFromApi() {
            $bill = Api::call($this->route, "results")[0];
            $this->setFromApi($bill);
        }
    }
}

?>