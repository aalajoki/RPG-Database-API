<?php

header("Access-Control-Allow-Origin: http://localhost/RPG-Database-API/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/core.php';
include_once '../config/database.php';
include_once 'class/player.php';

include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

$player = new Player($pdo);

// Get posted data
$data = json_decode(file_get_contents("php://input"));
 
// Set product property values
$player->email = $data->email;
$email_exists = $player->emailExists();



// Check that email exists and password is correct
if ($email_exists && password_verify($data->password, $player->password)) {
 
    $token = array(
       "iss" => $iss,
       "aud" => $aud,
       "iat" => $iat,
       "nbf" => $nbf,
       "data" => array(
           "id" => $player->id,
           "username" => $player->username,
           "email" => $player->email
       )
    );

    http_response_code(200);

    // Generate JWT
    $jwt = JWT::encode($token, $key);
    echo json_encode(
        array(
            "status" => 200,
            "body" => "Successful login.",
            "jwt" => $jwt
        )
    );
}
// Login failed
else {
    http_response_code(401);
    
    echo json_encode(
        array(
            "status" => 401,
            "body" => "Login failed.",
        )
    );
}