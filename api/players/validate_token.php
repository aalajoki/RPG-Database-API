<?php

header("Access-Control-Allow-Origin: http://localhost/RPG-Database-API/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 

include_once '../config/core.php';

include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// Get posted data
$data = json_decode(file_get_contents("php://input"));
 
// Get JWT
$jwt=isset($data->jwt) ? $data->jwt : "";
 
// JWT is not empty
if($jwt){
 
    // JWT is valid
    try {
        $decoded = JWT::decode($jwt, $key, array('HS256'));
 

        http_response_code(200);
 
        echo json_encode(array(
            "status" => 200,
            "body" => "Access granted.",
            "data" => $decoded->data
        ));
 
    }
    // JWT is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array(
            "status" => 401,
            "body" => "Access denied. Token is invalid."
            //"error" => $e->getMessage()
        ));
    }
}
// JWT is empty
else {
    http_response_code(401);

        echo json_encode(array(
            "status" => 401,
            "body" => "Access denied. Please log in."
            //"error" => $e->getMessage()
        ));
}