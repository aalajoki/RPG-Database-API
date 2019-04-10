<?php
// CORS headers and caching
header("Access-Control-Allow-Origin: http://localhost/RPG-Database-API/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Cache-Control: max-age=3600');

// For JWT configuration
require_once '../config/core.php';

// JWT encoding/decoding library
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

// Database connection and the required class
require_once '../config/database.php';
require_once 'class/player.php';

// Create a player object
$player = new Player($pdo);

// Get posted data from response body
$data = json_decode(file_get_contents("php://input"));
$player->email = $data->email;

// Return boolean True if the given email is being used by an account
$email_exists = $player->EmailExists();

// Check that email is in use and password is correct (compared to the password of the account found using the email)
if ($email_exists && password_verify($data->password, $player->password)) {
 
    // For generating the JWT using variables from config/core.php
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
    // Generate JWT and return it
    $jwt = JWT::encode($token, $key);
    echo json_encode(array(
        "status" => 200, 
        "body" => "Successful login.",
        "jwt" => $jwt
    ));
}
else {
    http_response_code(401);
    echo json_encode(array(
        "status" => 401, 
        "body" => "Login failed. Account does not exist or the password is incorrect."
    ));
}