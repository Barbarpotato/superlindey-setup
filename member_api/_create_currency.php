<?php

    // Get input data, assuming JSON POST
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // Prepare data for insertion
    $save_data = [
        'creator_user_id' => $input['creator_user_id'] ?? null,
        'creator_user_name' => $input['creator_user_name'] ?? null,
        'client_id' => $input['client_id'] ?? null,
        'client_name' => $input['client_name'] ?? '',
        'client_number' => $input['client_number'] ?? '',
        'is_confirmed' => $input['is_confirmed'] ?? 0,
        'confirmed_user_name' => $input['confirmed_user_name'] ?? null,
        'confirmed_user_identification' => $input['confirmed_user_identification'] ?? null,
        'confirmed_at' => $input['confirmed_at'] ?? null,
        'confirmed_note' => $input['confirmed_note'] ?? null,
        'is_rejected' => $input['is_rejected'] ?? 0,
        'rejected_user_name' => $input['rejected_user_name'] ?? null,
        'rejected_user_identification' => $input['rejected_user_identification'] ?? null,
        'rejected_at' => $input['rejected_at'] ?? null,
        'rejected_note' => $input['rejected_note'] ?? null,
        'currency_number' => 'LTY-26-01'
    ];


    // Insert the data
    $result = $DAMUREWARDS->currency->set($save_data);

    if ($result) {
        echo json_encode(['success' => true, 'id' => $result]);
    } else {
        echo json_encode(['error' => 'Failed to create currency']);
    }

?>