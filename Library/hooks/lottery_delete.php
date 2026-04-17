<?php
    // **
    // custom pre-hook goes there
    // ..
	
	if(count($DAMUREWARDS->lottery->get(array("id" => $id, "is_published" => 1 ))) > 0){
    	throw new Exception("cannot delete lottery data. lottery already published");
    }

	// delete lottery_client_coupon_allocation
	$filters = array();
	$filters["lottery_id"] = $id;
	foreach($DAMUREWARDS->lottery_client_coupon_allocation->get($filters) as $allocation){
		$DAMUREWARDS->lottery_client_coupon_allocation->delete($allocation['id']);    
    }

    // **
    // calling the parent function
    $res = parent::delete($id);

    // **
    // custom post-hook goes there
    // ..

	// tidak ada


    // **
    // done
    return $res;
?>