<?php
    // **
    // custom pre-hook goes there
    // ..
    if(!isset($save_data["creator_user_id"])){
    	throw new Exception("creator_user_id is required");
    }

    $validate_save_data = array(
        "client_id" => "Client Id"
    );

    foreach($validate_save_data as $key => $value){
        if(!isset($save_data[$key])){
            throw new Exception($value . " is required");
        }
    }
    
    // load object
	
	//$client = new Client();

    // validate the client data
    $filters = array();
    $filters["id"] = $save_data["client_id"];
    $client_list = $DAMUREWARDS->client->get($filters);
    if(count($client_list) == 0){
        throw new Exception("Client not found");
    }
    $client = $client_list[0];

    // **
    // calling the parent function
    $res = parent::set($save_data);

    // **
    // custom post-hook goes there
    // ..
    if ($res) {
        // This calls the set_ready method, which includes Library/hooks/lottery_set_ready.php
        $ready_result = $this->set_ready($res, 'ready');
        
		// $ready_result = $DAMUREWARDS->lottery->set_ready($res, 'ready');
        
        // Direct instantiation - autoloader loads Client.php automatically
        $client_data = [
            'client_name' => 'Client for Lottery ' . $result,
            'client_number' => 'C' . $result,
            // ... other required fields for client
        ];
        
    // This calls Client::set(), which includes Library/hooks/client_set.php
    // $client_result = $DAMUREWARDS->client->set($client_data);

    }

    // **
    // done
    return $res;
?>