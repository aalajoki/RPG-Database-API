<?php

header("Access-Control-Allow-Origin: http://localhost/RPG-Database-API/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Cache-Control: max-age=3600');

include_once '../config/core.php';

include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

include_once '../config/database.php';
include_once 'class/guild.php';
include_once '../characters/class/character.php';

// Get posted data & JWT
$data = json_decode(file_get_contents("php://input"));
$jwt = isset($data->jwt) ? $data->jwt : "";
 
if ($jwt) {
    // If JWT is not empty
    try {
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $id = $decoded->data->id;
         
        $guild = new Guild($pdo);
        $character = new Character($pdo);
        
        // Data from HTML into the creation function 
        $gm_id = $data->gm;
        $guild->name = $data->name;
        $guild->guild_type = $data->guild_type;
        $guild->description = $data->description;
        
        if (!Character.ValidateOwnership($gm_id, $id)) {
            http_response_code(403);
            $response = array(
                "status" => 403, 
                "body" => "Forbidden. You must own the character to make them a guild master.",
                "data" => $created_guild_id
            );
        }

        $created_guild_id = $guild->create($gm_id);
        
        if ($created_guild_id) {
            http_response_code(200);
            $response = array(
                "status" => 200, 
                "body" => "Guild has been created.",
                "data" => $created_guild_id
            );
        }
        else {
            http_response_code(400);
            $response = array(
                "status" => 400, 
                "body" => "Unable to create guild."
            );
        }
        echo json_encode($response);
    }
    // JWT is invalid
    catch (Exception $e) {
        http_response_code(401);
        echo json_encode(array(
            "status" => 401,
            "body" => "Access denied. Token is invalid.",
            //"error" => $e->getMessage()
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