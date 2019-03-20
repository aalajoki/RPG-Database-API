<?php
header("Content-Type:application/json");
include('pdo.php');

$statement = $pdo->prepare("SELECT * FROM player_character");
$statement->execute();
$results = $statement->fetchAll(PDO::FETCH_ASSOC);
$json = json_encode($results);
echo $json;