<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Cache-Control: max-age=3600');

require_once '../config/database.php';
require_once 'class/guild.php';

$guild = new Guild($pdo);

$results = $guild->ReadAll();

if (!$results) {
    http_response_code(404);
    echo json_encode(array(
        "status" => 404,
        "body" => "No guilds found."
    ));
}
else {
    http_response_code(200);
    echo json_encode(array(
        "status" => 200,
        "body" => "Guilds found.",
        "data" => $results
    ));
}