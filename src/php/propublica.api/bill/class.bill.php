<?php
require_once "propublica.api.php";
require_once "interface.api.object.php";

class Bill implements ProPublicaApiObject{
    use getAndPrintAsJson;
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
        $result = PROPUBLICA_API_CALL("$this->congress/bills/$this->bill_slug.json");
        $mem = $result["results"][0];
        $this->setFromApi($mem);
        $this->getUid();
    }

    function setFromApi($apiRes) {
        foreach ($apiRes as $key=>$value) $this->{$key} = $value;
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

?>