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
        }

        function fetchFromApi() {
            $result = Api::call("$this->congress/bills/$this->bill_slug.json");
            if (isset($result) && isset($result["results"]) && isset($result["results"][0])) {
                $bill = $result["results"][0];
                $this->setFromApi($bill);
                $this->getUid();
            } else throw new \Exception("ProPublica.Api => $this->congress/bills/$this->bill_slug.json returned null value");
        }

        function getUid() {
            if (isset($this->uid)) return $this->uid;
            else {
                $num = substr($this->bill_slug, strlen($this->bill_type));
                $this->uid = "bill.$this->congress.$this->bill_type.$num";
            }
            return $this->uid;
        }
    }
}

?>