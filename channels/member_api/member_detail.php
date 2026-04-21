<?php
    $data = $_GET;

    // **
    // Prepare the data here
    // eg.
    $filters = array();
    $filters["id"] = 1;
    $loyalty_user_list = $loyalty->loyalty_user->get($filters);
    if (count($loyalty_user_list) == 0) {
        echo json_encode(array("message" => "User not found"));
        exit;
    }
    $user = $loyalty_user_list[0];

    // validate the client data
    $filters = array();
    $filters["id"] = $user["client_id"];
    $client_list = $DAMUREWARDS->client->get($filters);
    if (count($client_list) == 0) {
        echo json_encode(array("message" => "Client not found"));
        exit;
    }
    $client = $client_list[0];

    // **
    // build json here
    $filters = array();
    $filters["user_id"] = $user["id"];
    $token_list = $loyalty->loyalty_token->get($filters);

    $response = array(
        "user" => $user,
        "token" => $token_list,
        "client" => $client
    );

    echo json_encode($response);
?>