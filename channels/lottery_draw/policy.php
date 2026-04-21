<?php

/**
 * ============================================================
 * POLICY MIDDLEWARE
 * ============================================================
 * 
 * Acts like a middleware layer.
 * 
 * ⚠️ IMPORTANT:
 * Every request BEFORE reaching any endpoint logic
 * MUST include this file.
 * 
 * If any validation fails, execution will be stopped
 * by throwing an Exception.
 * 
 * ============================================================
 */

// ========= EXAMPLE =========
// Validate required header / public data
if (!isset($_PUBLIC['member_number'])) {
    throw new Exception("invalid request headers");
}

// Fetch member data (replace YOUR_GROUP_OBJECT_NAME with your actual object)
$member = $YOUR_GROUP_OBJECT_NAME->member->get([
    "member_number" => $_PUBLIC['member_number']
]);

// Validate member existence
if (count($member) == 0) {
    throw new Exception('member not found!');
}

?>