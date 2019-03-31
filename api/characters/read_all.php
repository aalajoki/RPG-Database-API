<?php
header("Content-Type:application/json");

require_once('../database.php'); 


$statement = $pdo->prepare(
"SELECT a.id, a.name, c.race, b.class, a.level, e.name AS guild
FROM player_character a
LEFT JOIN character_race c ON a.char_race = c.id
LEFT JOIN character_class b ON a.char_class = b.id
LEFT JOIN guild_membership d ON a.id = d.char_id
LEFT JOIN guild e ON d.guild_id = e.id"
);

$statement->execute();
$results = $statement->fetchAll(PDO::FETCH_ASSOC);


if (!$results) {
    $error = array(
        "status" => 404, 
        "body" => "No characters found."
    );
    http_response_code(404);
    $json = json_encode($error);
}
else {
    $json = json_encode($results);
}

echo $json;
die();