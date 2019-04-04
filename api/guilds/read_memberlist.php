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
    $error = array(
        "status" => 400, 
        "body" => "Invalid request. Please ensure that the URL and guild ID are correct."
    );
    http_response_code(400);
    $json = json_encode($error);
}
else {
    $statement = $pdo->prepare(
        "SELECT b.id, b.name, c.rank, a.joined
        FROM guild_membership a
        LEFT JOIN player_character b
        ON a.char_id = b.id
        LEFT JOIN guild_rank c
        ON a.char_rank = c.id
        WHERE a.guild_id = ?"
    );

    try {
        $statement->bindParam(1, $id, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e) {
        echo json_encode($e);
    }


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
            "body" => "Memberlist query successful.",
            "data" => $results
        ));
    }
}