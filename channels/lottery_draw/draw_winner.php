<?php
    $data = $_GET;

   	$random_winner = $DAMUREWARDS->lottery->random_winner();

	$res = array(
    	"status" => "success",
        "winner_number" => $random_winner 
    );

    ob_clear();
    echo json_encode($res);
?>