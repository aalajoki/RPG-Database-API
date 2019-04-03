<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Cache-Control: max-age=3600');

require_once('../config/database.php'); 


$statement = $pdo->prepare(
    "SELECT a.id, a.name, b.type, a.description
    FROM guild a
    LEFT JOIN guild_type b
    ON a.guild_type = b.id"
);

$statement->execute();
$results = $statement->fetchAll(PDO::FETCH_ASSOC);

if (!$results) {
    $error = array(
        "status" => 404, 
        "body" => "No guilds found."
    );
    http_response_code(404);
    $json = json_encode($error);
}
else {
    $json = json_encode($results);
}

echo $json;
die();