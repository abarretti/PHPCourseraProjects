<?php
require_once "pdo.php";
session_start();

$term = $_GET['term'];
error_log("Looking up typeahead term=".$term);

$stmt = $pdo->prepare('SELECT name
FROM Institution
WHERE name LIKE :prefix');
$stmt->execute([':prefix' => $term."%"]);

$retval = [];
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $retval[] = $row['name'];
}

echo(json_encode($retval, JSON_PRETTY_PRINT));
