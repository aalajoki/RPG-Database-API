<?php

header("Access-Control-Allow-Origin: *");
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

require_once '../config/database.php';
require_once 'class/character.php';

$character = new Character($pdo);

// Get data from the request
$data = json_decode(file_get_contents("php://input"));

// Get JWT
$jwt = isset($data->jwt) ? $data->jwt : "";
 

if ($jwt) {
    // If JWT is not empty
    try {
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        
        $id = $decoded->data->id;
        
        try {
            $query_results = $character->ReadOwnedGuildless($id);
        }
        catch (Exception $e) {
            echo $e;
        }

        if ($query_results) {
            http_response_code(200);
            $response = array(
                "status" => 200, 
                "body" => "Guildless characters found.",
                "data" => $query_results
            );
        }
        else {
            http_response_code(404);
            $response = array(
                "status" => 404, 
                "body" => "No guildless characters found."
            );
        }

        $json = json_encode($response);
        echo $json;
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