<?php
header("Content-Type:application/json");

require_once('../database.php'); 


$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id == FALSE || $id == NULL) {
    $error = array(
        "status" => 400, 
        "body" => "Invalid request. Please ensure that the guild ID is correct."
    );
    http_response_code(400);
    $json = json_encode($error);
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
        $error = array(
            "status" => 404, 
            "body" => "No guilds found with the chosen ID $id"
        );
        http_response_code(404);
        $json = json_encode($error);
    }
    else {
        $json = json_encode($results);
    }
}

echo $json;
die();