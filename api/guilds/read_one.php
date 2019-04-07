<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Cache-Control: max-age=3600');

require_once('../config/database.php'); 


$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id == FALSE || $id == NULL) {
    http_response_code(400);
    echo json_encode(array(
        "status" => 400,
        "body" => "Invalid request. Please ensure that the guild ID is correct."
    ));
}
else {
    $statement = $pdo->prepare(
        "SELECT a.id, a.name, b.type, a.description
        FROM guild a
        LEFT JOIN guild_type b
        ON a.guild_type = b.id
        WHERE a.id=?"
    );

    $statement->bindParam(1, $id, PDO::PARAM_INT);
    $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);


    if (!$results) {
        http_response_code(404);
        echo json_encode(array(
            "status" => 404,
            "body" => "No guilds found with the chosen ID $id"
        ));
    }
    else {
        http_response_code(200);
        echo json_encode(array(
            "status" => 200,
            "body" => "Character query successful.",
            "data" => $results
        ));
    }
}