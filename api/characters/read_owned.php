<?php
// CORS headers + caching
header("Access-Control-Allow-Origin: http://localhost/RPG-Database-API/");
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
 
// Get posted data and JWT
$data = json_decode(file_get_contents("php://input"));
$jwt = isset($data->jwt) ? $data->jwt : "";
 
if ($jwt) {
    // JWT is not empty, attempt to decode
    try {
        $decoded = JWT::decode($jwt, $key, array('HS256'));
    }
    catch (Exception $e){
        // JWT is invalid
        http_response_code(401);
        echo json_encode(array(
            "status" => 401,
            "body" => "Access denied. Token is invalid.",
            //"error" => $e->getMessage()
        ));
        die();
    }
    
    // Get player ID from the decoded JWT
    $id = $decoded->data->id;

    // Create a character object with the database connection
    $character = new Character($pdo);

    // Get characters owned by the authenticated player
    $results = $character->ReadOwned($id);

    if ($results) {
        http_response_code(200);
        echo json_encode(array(
            "status" => 200,
            "body" => "Owned characters found.",
            "data" => $results
        ));
    }
    else {
        http_response_code(404);
        echo json_encode(array(
            "status" => 404,
            "body" => "No owned characters found."
        ));
    }
}
else {
    // JWT is empty
    echo json_encode(array(
        "status" => 401,
        "body" => "Access denied. Please log in."
        //"error" => $e->getMessage()
    ));
}