<?php

if(!isset($_PUBLIC['member_number'])){
	throw new Exception("invalid request headers");
}

$member = $DAMUREWARDS->member->get(array("member_number" => $_PUBLIC['MEMBER_NUMBER']));

if(count($member) == 0){
	throw new Exception ('member not found!');
}else {
	$member = $member[0];
    if($member['is_active'] == 0){
    	throw new Exception('member is not active. contact admin!');
    }
}


?>