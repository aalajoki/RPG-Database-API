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

// Database connection and the required class
require_once '../config/database.php';
require_once 'class/character.php';

// Creating a player object with the database connection
$character = new Character($pdo);

// Get data from the request
$data = json_decode(file_get_contents("php://input"));

// Get JWT
$jwt = isset($data->jwt) ? $data->jwt : "";
 

if ($jwt) {
    // JWT is not empty
    try {
        // Attempt to decode
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
    
    // Get ID of the authenticated player from the JWT    
    $id = $decoded->data->id;

    // Get guildless characters owned by the authenticated player
    $query_results = $character->ReadOwnedGuildless($id);

    if ($query_results) {
        http_response_code(200);
        echo json_encode(array(
            "status" => 200, 
            "body" => "Owned guildless characters found.",
            "data" => $query_results
        ));
    }
    else {
        http_response_code(404);
        echo json_encode(array(
            "status" => 404, 
            "body" => "No owned guildless characters found."
        ));
    }
}
else {
    // JWT is empty
    http_response_code(401);
    echo json_encode(array(
            "status" => 401,
            "body" => "Access denied. Please log in."
            //"error" => $e->getMessage()
    ));
}