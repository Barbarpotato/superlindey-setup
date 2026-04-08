<?php
    // **
    // custom pre-hook goes there
    // ..
    // if(!isset($save_data["creator_user_id"])){
    //     throw new Exception("creator_user_id is required");
    // }

    // **
    // calling the parent function
    $res = parent::set($save_data);

    // **
    // custom post-hook goes there
    // ..
    if ($res) {
        // This calls the set_ready method, which includes Library/hooks/lottery_set_ready.php
        $ready_result = $this->set_ready($res, 'ready');

        // Direct instantiation - autoloader loads Client.php automatically
        $client = new Client();
        $client_data = [
            'client_name' => 'Client for Lottery ' . $result,
            'client_number' => 'C' . $result,
            // ... other required fields for client
        ];
        
    // This calls Client::set(), which includes Library/hooks/client_set.php
    $client_result = $client->set($client_data);

    }

    // **
    // done
    return $res;
?>