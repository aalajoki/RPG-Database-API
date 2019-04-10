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

// Database connection and the required classes
include_once '../config/database.php';
include_once 'class/guild.php';
include_once '../characters/class/character.php';

// Get posted data and JWT
$data = json_decode(file_get_contents("php://input"));
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
    $player_id = $decoded->data->id;
        
    // Create a new guild object with the database connection
    $guild = new Guild($pdo);

    // Data from the request body into the creation function 
    $guild->id = $data->id;
    $guild->name = $data->name;
    $guild->guild_type = $data->guild_type;
    $guild->description = $data->description;
        
    // Verify that the authenticated player owns the guild master character of the guild to be edited
    if (!$guild->ValidateOwnership($guild->id, $player_id)) {
        
        http_response_code(403);
        echo json_encode(array(
            "status" => 403, 
            "body" => "Forbidden. You must own the guild's GM character in order to edit it."
        ));
        
    }
    else {
        // Edit the guild with the given ID, return boolean True on success
        $edited = $guild->Edit($guild->id);
    
        if ($edited) {
            http_response_code(200);
            echo json_encode(array(
                "status" => 200, 
                "body" => "Guild info has been updated."
            ));
        }
        else {
            http_response_code(400);
            echo json_encode(array(
                "status" => 400, 
                "body" => "Unable to update guild."
            ));
        }
        
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