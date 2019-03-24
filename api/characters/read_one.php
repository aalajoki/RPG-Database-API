<?php
header("Content-Type:application/json");

require_once('../../database.php'); 


$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id == FALSE || $id == NULL) {
    $error = array(
        "code" => 400, 
        "body" => "Invalid request. Please ensure that the character ID is correct."
    );
    http_response_code(400);
    $json = json_encode($error);
    
    echo $json;
    die();
    //include("errors/400.php");
}

$statement = $pdo->prepare("SELECT * FROM player_character WHERE ID=?");
$statement->bindParam(1, $id, PDO::PARAM_INT);
$statement->execute();
$results = $statement->fetchAll(PDO::FETCH_ASSOC);


if (!$results) {
    $error = array(
        "code" => 404, 
        "body" => "No characters found with the chosen ID $id"
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