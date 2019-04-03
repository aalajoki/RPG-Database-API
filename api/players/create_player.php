<?php

header("Access-Control-Allow-Origin: http://localhost/RPG-Database-API/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once 'player.php';

$player = new Player($pdo);

// Get data from the request
$data = json_decode(file_get_contents("php://input"));
 
$player->username = $data->username;
$player->email = $data->email;
$player->password = $data->password;
 
if ($player->create()){
 
    $response = array(
        "status" => 200, 
        "body" => "Player account has been created."
    );
    http_response_code(200);
}
else {
    $response = array(
        "status" => 400, 
        "body" => "Unable to create player account."
    );
    http_response_code(400);
}

$json = json_encode($response);
echo $json;