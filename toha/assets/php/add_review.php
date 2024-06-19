<?php

require_once __DIR__.'/boot.php';

$stmt = pdo()->prepare("INSERT INTO reviews (name, text, date, stars) VALUES (:name, :text, :date, :stars)");
$stmt->execute([':name' => $_POST['name'],':text' => $_POST['text'],':date' => $_POST['date'],':stars' => $_POST['stars']]);
$id = pdo()->lastInsertId();
echo $id;
?>