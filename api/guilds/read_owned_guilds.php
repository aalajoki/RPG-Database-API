<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Cache-Control: max-age=3600');

include_once '../config/core.php';

include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

require_once('../config/database.php'); 

$data = json_decode(file_get_contents("php://input"));
$jwt = isset($data->jwt) ? $data->jwt : "";
 
// JWT is not empty
if ($jwt) {
 
    // Attempt to decode & create character on success
    try {
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        
        // Set data for SQL statement
        $id = $decoded->data->id;

        $statement = $pdo->prepare(
            "SELECT a.id, a.name as 'guild_name', d.type, a.description, c.name as 'gm_name'
            FROM guild a
            LEFT JOIN guild_membership b
            ON a.id = b.guild_id 
            LEFT JOIN player_character c
            ON b.char_id = c.id
            LEFT JOIN guild_type d
            ON a.guild_type = d.id
            WHERE c.player_id = ? AND b.char_rank = 5"
        );
        try {
            $statement->bindParam(1, $id, PDO::PARAM_INT);
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            echo json_encode($e);
            die();
        }
        if (!$results) {
            $error = array(
                "status" => 404, 
                "body" => "No owned guilds found."
            );
            http_response_code(404);
            $json = json_encode($error);
        }
        else {
            http_response_code(200);
            echo json_encode(array(
                "status" => 200,
                "body" => "Guild query successful.",
                "data" => $results
            ));
        }
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