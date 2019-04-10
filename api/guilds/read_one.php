<?php
// CORS headers and caching
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Cache-Control: max-age=3600');

// Database connection and required class
require_once '../config/database.php'; 
require_once 'class/guild.php';

// Create a guild object with the required database connection
$guild = new Guild($pdo);

// Get ID from query string
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id == FALSE || $id == NULL) {
    http_response_code(400);
    echo json_encode(array(
        "status" => 400,
        "body" => "Invalid request. Please ensure that the guild ID is correct."
    ));
}
else {
    // Search for a guild with the ID parameter, returns a boolean True on success
    $results = $guild->ReadOne($id);

    if (!$results) {
        http_response_code(404);
        echo json_encode(array(
            "status" => 404,
            "body" => "No guilds found with the chosen ID $id"
        ));
    }
    else {
        http_response_code(200);
        echo json_encode(array(
            "status" => 200,
            "body" => "Guild found.",
            "data" => $results
        ));
    }
}