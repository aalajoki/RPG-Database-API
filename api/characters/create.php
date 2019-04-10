<?php
// CORS headers and caching
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Cache-Control: max-age=3600');

// For JWT configuration
include_once '../config/core.php';

// JWT encoding/decoding library
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

// Database connection and the required classes
require_once '../config/database.php'; 
require_once '../players/class/player.php';
require_once 'class/character.php';

 
// Get posted data from response body and JWT
$data = json_decode(file_get_contents("php://input"));
$jwt = isset($data->jwt) ? $data->jwt : "";
 

if ($jwt) {
    // If JWT is not empty
    try {
        $decoded = JWT::decode($jwt, $key, array('HS256'));
    }
    // JWT is invalid
    catch (Exception $e) {
        http_response_code(401);
        echo json_encode(array(
            "status" => 401,
            "body" => "Access denied. Token is invalid.",
            //"error" => $e->getMessage()
        ));
        die();
    }
    
    // Create a character object with the database connection
    $character = new Character($pdo);
        
    // Data from the request body into the character object
    $character->player_id = $decoded->data->id;
    $character->name = $data->name;
    $character->char_race = $data->char_race;
    $character->char_class = $data->char_class;

    // Create a character, return ID if successful
    $created_id = $character->Create();

    if ($created_id) {
        http_response_code(201);
        echo json_encode(array(
            "status" => 201,
            "body" => "Character created.",
            "character_id" => $created_id
        ));
    }
    else {
        http_response_code(400);
        echo json_encode(array(
            "status" => 400,
            "body" => "Character creation failed."
        ));
    }
}
// If JWT is empty
else {
    http_response_code(401);
    echo json_encode(array(
            "status" => 401,
            "body" => "Access denied. Please log in."
            //"error" => $e->getMessage()
    ));
}