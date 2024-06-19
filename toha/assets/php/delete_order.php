<?php

require_once __DIR__.'/boot.php';

$stmt = pdo()->prepare("DELETE FROM orders WHERE `id` = :orderid;");
$stmt->execute([':orderid' => $_POST['orderid']]);

?>