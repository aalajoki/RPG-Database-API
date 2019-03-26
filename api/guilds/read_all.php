<?php
header("Content-Type:application/json");

require_once('../../database.php'); 


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
    
    echo $json;
    die();
}
else {
    $json = json_encode($results);
    
    echo $json;
    die();
}