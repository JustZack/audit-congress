<?php

function GetMembers() {
    //$members = API_CALL("member");
    $members = API_CALL_BULK("member", "");
    //Member list has imageUrl format problems, so fix that
    $members["members"] = format_MembersData($members["members"]);
    return $members;
}
function GetMember($id) {
    $member = API_CALL("member/$id");
     //Single member has correct imageUrl format
    //$member["member"] = format_MemberData($member["member"]);
    return $member;
}
function GetMemberSponsoredLegislation($id) {
    //$sponsored = API_CALL("member/$id/sponsored-legislation");
    $sponsored = API_CALL_BULK("member","$id/sponsored-legislation");
    return $sponsored;
}
function GetMemberCoSponsoredLegislation($id) {
    //$cosponsored = API_CALL("member/$id/cosponsored-legislation");
    $cosponsored = API_CALL_BULK("member","$id/cosponsored-legislation");
    return $cosponsored;
}

/*
    Format up data returned by the /member/ endpoint
    1. Fix imageUrl to use direct link.
    2. Change name from Last, First to First Last.
    3. Change served from an array of served times to string.
*/
//Fix imageUrl to use direct link.
function format_image_MemberData($imageUrl) {
    $url_pos = strrpos($imageUrl, "https://");
    $url = substr($imageUrl, $url_pos);
    return $url;
}
function format_MemberData($member) {
    //Extract imageUrl from imageUrl field
    $member["depiction"]["imageUrl"] = format_image_MemberData($member["depiction"]["imageUrl"]);
    return $member;
}
function format_MembersData($members) {
    $members_array = array();

    foreach ($members as $member)
        $members_array[] = format_MemberData($member);

        return $members_array;
}

?>