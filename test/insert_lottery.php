<?php

// Include database configuration (includes LindseyEngine and autoloader)
include '../_db_config.php';

// Create a lottery instance (autoloader loads Lottery wrapper class)
$lottery_engine = new Lottery();

// Sample data for a new lottery
$lottery_data = [
    'creator_user_id' => 'admin',
    'creator_user_name' => 'Administrator',
    'lottery_number' => 'LOT-2024-001',
    'event_name' => 'Summer Mega Lottery 2024',
    'description' => 'Join our exciting summer lottery with amazing prizes!',
    'period_start' => '2024-06-01 00:00:00',
    'period_end' => '2024-08-31 23:59:59',
    'max_claimed_per_user' => 5,
    'max_awarding_per_user' => 3,
    'next_serial_number' => 1,
    'cut_off_days' => 30,
    'client_id' => 1,
    'is_displayed' => true
];

try {
    // Insert the lottery
    $new_lottery_id = $lottery_engine->set($lottery_data);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>