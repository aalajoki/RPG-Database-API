<?php
header("Content-Type:application/json");

require_once('../../database.php'); 


$statement = $pdo->prepare("SELECT * FROM player_character");
$statement->execute();
$results = $statement->fetchAll(PDO::FETCH_ASSOC);


if (!$results) {
    $error = array(
        "code" => 404, 
        "body" => "No characters found."
    );
    http_response_code(404);
    $json = json_encode($error);
    
    echo $json;
    die();
}
else {
    $json = json_encode($results);
    
    echo $json;
    die();
}