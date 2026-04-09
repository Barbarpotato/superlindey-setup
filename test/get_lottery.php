<?php

// Include database configuration (includes LindseyEngine and autoloader)
include '../_db_config.php';

// Create a lottery instance (autoloader loads Lottery wrapper class)
$lottery_engine = new Lottery();

// Test getting lottery data filtered by lottery_number
$lottery_number = 'LOT-2024-002'; // Example lottery number

try {
    // Get lottery data filtered by lottery_number
    $lotteries = $lottery_engine->get(['event_name' => "Summer Mega Lottery 2025"]);

    if (!empty($lotteries)) {
        printr($lotteries);
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>