<?php

require_once __DIR__.'/boot.php';

$stmt = pdo()->prepare("UPDATE `orders` SET `status` = 'Доставка' WHERE `login` = :username AND `id` = :order;");
$stmt->execute([':username' => $_POST['login'],':order' => $_POST['id']]);

?>