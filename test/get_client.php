<?php

// Include database configuration (includes LindseyEngine and autoloader)
include '../_db_config.php';

// Create a client instance
$client = new Client();

// Test getting all clients
$all_clients = $client->get([]);
printr($all_clients);

// Test getting client by id=1
$client_by_id = $client->get(['id' => 1]);
printr($client_by_id);

?>