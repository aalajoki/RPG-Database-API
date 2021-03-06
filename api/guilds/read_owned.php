<?php
// CORS headers and caching
header("Access-Control-Allow-Origin: http://localhost/RPG-Database-API/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Cache-Control: max-age=3600');

// For JWT config
include_once '../config/core.php';

// JWT library
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

// Database connection and the required class
require_once('../config/database.php');
require_once 'class/guild.php';

// Get posted data from request body & JWT
$data = json_decode(file_get_contents("php://input"));
$jwt = isset($data->jwt) ? $data->jwt : "";

if ($jwt) {
    // JWT is not empty
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
    
    // Get player ID from the decoded JWT
    $id = $decoded->data->id;
    
    // Create a guild object with the required database connection
    $guild = new Guild($pdo);

    $results = $guild->ReadOwned($id);
        
    if ($results) {
        http_response_code(200);
        echo json_encode(array(
            "status" => 200,
            "body" => "Owned guilds found.",
            "data" => $results
        ));
    }
    else {
        http_response_code(404);
        echo json_encode(array(
            "status" => 404, 
            "body" => "No owned guilds found."
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