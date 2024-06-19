<?php

require_once __DIR__.'/boot.php';

$stmt = pdo()->prepare("SELECT * FROM `orders` WHERE `product` = :product AND `login` = :id AND `status` = 'Не заказан';");
$stmt->execute([':product' => $_POST['product'],':id' => $_POST['login']]);
if ($stmt->rowCount() > 0) {
    echo 'BAD';
    die;
}

$stmt = pdo()->prepare("INSERT INTO orders (login, adress, phone, product, price, status) VALUES (:username, :adress, :phone, :product, :price, :status)");
$stmt->execute([':username' => $_POST['login'],':adress' => 'Неизвестно',':phone' => 'Неизвестно',':product' => $_POST['product'],':price' => $_POST['price'],':status' => 'Не заказан']);
echo 'GOOD';

?>