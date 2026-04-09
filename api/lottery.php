<?php
	$lottery_engine = new Lottery();
	
	// get the lottery data
	$filters = array();
	$filters['id'] = 14;
	$lottery_list = $lottery_engine->get($filters);
	echo json_encode($lottery_list);
?>