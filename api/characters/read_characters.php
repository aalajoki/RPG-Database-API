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

        $statement = $pdo->prepare(
            "SELECT a.id, a.name, c.race, b.class, a.level, e.name AS guild
            FROM player_character a
            LEFT JOIN character_race c ON a.char_race = c.id
            LEFT JOIN character_class b ON a.char_class = b.id
            LEFT JOIN guild_membership d ON a.id = d.char_id
            LEFT JOIN guild e ON d.guild_id = e.id
            WHERE a.player_id=?"
        );
        $statement->bindParam(1, $id, PDO::PARAM_INT);
        
        try {
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            echo json_encode($e);
            die();
        }

        http_response_code(200);
        echo json_encode(array(
            "status" => 200,
            "body" => "Character query successful.",
            "data" => $results
        ));
    }
    // JWT is invalid
    catch (Exception $e){
        // set response code
        http_response_code(401);
        // show error message
        echo json_encode(array(
            "status" => 401,
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}
// If JWT is empty
else {

    http_response_code(401);
    echo json_encode(array("message" => "Access denied."));
}