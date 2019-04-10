<?php
// CORS headers and caching
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Cache-Control: max-age=3600');

// Database connection
require_once '../config/database.php'; 

// Get ID from query string
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
        "SELECT a.id, a.name, c.race, b.class, a.level, e.name AS guild
        FROM player_character a
        LEFT JOIN character_race c ON a.char_race = c.id
        LEFT JOIN character_class b ON a.char_class = b.id
        LEFT JOIN guild_membership d ON a.id = d.char_id
        LEFT JOIN guild e ON d.guild_id = e.id
        WHERE a.id=?"
    );

    // Bind parameter into the prepared statement
    $statement->bindParam(1, $id, PDO::PARAM_INT);
    
    $statement->execute();
    
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);

    if (!$results) {
        http_response_code(404);
        echo json_encode(array(
            "status" => 404,
            "body" => "No characters found with the chosen ID $id"
        ));
    }
    else {
        http_response_code(200);
        echo json_encode(array(
            "status" => 200,
            "body" => "Character found.",
            "data" => $results
        ));
    }
}