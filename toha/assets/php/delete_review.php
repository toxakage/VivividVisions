<?php

require_once __DIR__.'/boot.php';

$stmt = pdo()->prepare("DELETE FROM reviews WHERE `id` = :id;");
$stmt->execute([':id' => $_POST['id']]);

?>