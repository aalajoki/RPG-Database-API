<?php
// CORS headers and caching
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Cache-Control: max-age=3600');

// Database connection and the required class
require_once '../config/database.php'; 
require_once 'class/character.php';

// Create a character object with the required database connection
$character = new Character($pdo);

// Get all characters from the database
$results = $character->ReadAll();

if (!$results) {
    http_response_code(404);
    echo json_encode(array(
        "status" => 404,
        "body" => "No characters found."
    ));
}
else {
    http_response_code(200);
    echo json_encode(array(
        "status" => 200,
        "body" => "Characters found.",
        "data" => $results
    ));
}