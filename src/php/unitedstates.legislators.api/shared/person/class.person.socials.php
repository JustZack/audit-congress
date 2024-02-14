<?php

namespace UnitedStatesLegislators {
    class PersonSocials extends \AuditCongress\ApiChildObject {
        public
            $twitter,
            $twitter_id,
            $facebook,
            $facebook_id,
            $youtube,
            $youtube_id,
            $instagram,
            $instagram_id;
        
        function __construct($bioObj) {
            $this->setFieldsFromObject($bioObj);
        }
    }
}

?>
