<?php

    $client = new Client();
    $client_list = $client->get(['id' => 4]);

    echo json_encode($client_list);


?>