<?php

require_once __DIR__.'/boot.php';

$stmt = pdo()->prepare("INSERT INTO products (title, description, price,category) VALUES (:title, :desc, :price, :category)");
$stmt->execute([':title' => $_POST['title'],':desc' => $_POST['desc'],':price' => $_POST['price'],':category' => $_POST['category']]);
$id = pdo()->lastInsertId();
echo $id;
?>