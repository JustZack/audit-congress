<?php

define("PROPUBLICA_FOLDER", __DIR__);

/* Use this to simplify only load the needed files
Using $className => Namespace\Classname
determine the actual file path of the class
then require it
spl_autoload_register(function ($className) {
    $parts = explode('\\', $className);
    $namespace = $parts[0];
    $class = $parts[1];
    $filename = PROPUBLICA_FOLDER."\\$namespace\\class." . $class . ".php";
    if (is_readable($filename)) {
        require $filename;
    }
});*/

require_once AUDITCONGRESS_FOLDER."\\abstract.api.object.php";

require_once PROPUBLICA_FOLDER."\\api\\propublica.api.php";
require_once PROPUBLICA_FOLDER."\\member\\class.member.php";
require_once PROPUBLICA_FOLDER."\\member\\class.member.votes.php";
require_once PROPUBLICA_FOLDER."\\bill\\class.bill.php";
require_once PROPUBLICA_FOLDER."\\vote\\class.vote.php";
require_once PROPUBLICA_FOLDER."\\committee\\class.committee.php";

?>