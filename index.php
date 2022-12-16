<html>
    <head>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.3/js/bootstrap.min.js" integrity="sha512-1/RvZTcCDEUjY/CypiMz+iqqtaoQfAITmNSJY17Myp4Ms5mdxPS5UV7iOfdZoxcGhzFbOm6sntTKJppjvuhg4g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </head>
    <body>

    <div id="react_root">
        
    </div>

    <?php

    include_once "src/congress.api/api.php";
    include_once "src/congress.classes/class.bill.php";
    include_once "src/congress.classes/class.member.php";

    //$json = GetCongresses();
    //print_r($json);
    //$json = GetBillsByCongress("117");
    //print_r($json["bills"][5]);
    //$json = GetBill("117", "HR", 8404);
    //print_r($json["bill"]);
    //$json = GetBillActions("117", "HR", 8404);
    //print_r($json);
    //$json = GetBillTitles("117", "HR", 8404);
    //print_r($json);
    //$json = GetBill("117", "SRES", 869);
    //print_r($json["bill"]);
    ///$json = GetMember("N000002");
    //$json = GetMembers();
    //print_r($json);
    //$json = GetMember("E000259");
    //print_r($json);
    //$json = GetMemberSponsoredLegislation("E000259");
    //print_r($json);
    //$json = GetMemberCoSponsoredLegislation("E000259");
    //print_r($json);
    //$json = GetBills();
    //print_r($json);
    //$json = GetAmendments();
    //print_r(Bill::Get("117", "HR", "521"));
    //$bill = $json["bills"][0];
    //print_r(GetBillCoSponsors($bill["congress"], $bill["type"], $bill["number"]));

    ?>

    </body>
    <footer>
        <script crossorigin src="https://unpkg.com/react@18/umd/react.development.js"></script>
        <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
        <script src='dist/main.bundle.js'></script>
        <!--
        <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
        <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
        -->
    </footer>
</html>