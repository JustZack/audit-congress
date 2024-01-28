<?php

function GetCongresses(){
    $congresses = API_CALL("congress");
    return $congresses;
}
function GetCongress($cession){
    $congress = API_CALL("congress/$congress");
    return $congress;
}

?>