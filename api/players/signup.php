<?php
// CORS headers
header("Access-Control-Allow-Origin: http://localhost/RPG-Database-API/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Database connection and required class
require_once '../config/database.php';
require_once 'class/player.php';

// Create a player object with the required database connection
$player = new Player($pdo);

// Get data from the request
$data = json_decode(file_get_contents("php://input"));

// Data from HTML into the creation function
$player->username = $data->username; // CHECK THAT THESE AREN'T EMPTY
$player->email = $data->email;
$player->password = $data->password;

// Call the player account creation function, which returns boolean True on success
$player_id = $player->Create();
 
if ($player_id) {
    http_response_code(200);
    echo json_encode(array(
        "status" => 200, 
        "body" => "Player account created.",
        "player_id" => $player_id // Return ID of the created player account
    ));
}
else {
    http_response_code(400);
    echo json_encode(array(
        "status" => 400, 
        "body" => "Unable to create player account."
    ));
}