<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Cache-Control: max-age=3600');

require_once('../config/database.php');
require_once 'class/guild.php';

$guild = new Guild($pdo);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id == FALSE || $id == NULL) {
    http_response_code(400);
    echo json_encode(array(
        "status" => 400, 
        "body" => "Invalid request. Please ensure that the URL and guild ID are correct."
    ));
}
else {
    $results = $guild->ReadMembers($id);

    if (!$results) {
        $error = array(
            "status" => 404, 
            "body" => "No guilds found with the chosen ID $id"
        );
        http_response_code(404);
        $json = json_encode($error);
    }
    else {
        http_response_code(200);
        echo json_encode(array(
            "status" => 200,
            "body" => "Members found.",
            "data" => $results
        ));
    }
}