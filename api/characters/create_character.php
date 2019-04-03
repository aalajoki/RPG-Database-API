<?php

header("Access-Control-Allow-Origin: *");
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
 
// files needed to connect to database
require_once('../config/database.php'); 
include_once '../players/player.php';

//REFACTOR INTO CHARACTER FILES INTO A CHARACTER OBJECT?
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = isset($data->jwt) ? $data->jwt : "";
 
// JWT is not empty
if ($jwt) {
 
    // Attempt to decode & create character on success
    try {

        $decoded = JWT::decode($jwt, $key, array('HS256'));
        
        // Set data for SQL statement
        $id = $decoded->data->id;
        $name = $data->name;
        $name = '"' . $name . '"';
        $char_race = $data->char_race;
        $char_class = $data->char_class;

    
        $statement = $pdo->prepare(
            "INSERT INTO Player_Character (player_id, name, char_race, char_class)
            VALUES ($id, $name, $char_race, $char_class);"
        );
        try {
            $statement->execute();
        }
        catch (PDOException $e) {
            echo json_encode($e);
            die();
        }

        http_response_code(201);
        echo json_encode(array(
            "status" => 201,
            "body" => "Character created."
        ));
    }
    // JWT is invalid
    catch (Exception $e){
        // set response code
        http_response_code(401);
        // show error message
        echo json_encode(array(
            "status" => 401,
            "body" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}
// If JWT is empty
else {

    http_response_code(401);
    echo json_encode(array("message" => "Access denied."));
}