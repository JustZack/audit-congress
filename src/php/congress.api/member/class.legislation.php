<?php
namespace CongressGov {
    class Legislation extends \AuditCongress\ApiChildObject {
        public
            $congress,
            $number,
            $type,

            $introducedDate,
            $latestAction,
            
            $policyArea,
            $title;

            function __construct($actionObject) {
                $this->setFieldsFromObject($actionObject);
                $this->lowerCaseField("type");
                $this->unsetField("url");
            }
    }
}