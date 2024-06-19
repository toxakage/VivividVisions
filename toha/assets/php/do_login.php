<?php

require_once __DIR__.'/boot.php';

$stmt = pdo()->prepare("SELECT * FROM `users` WHERE `login` = :username");
$stmt->execute([':username' => $_POST['username']]);
if (!$stmt->rowCount()) {
    echo '0';
    die;
}
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (password_verify($_POST['password'], $user['password'])) {
    if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
        $newHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = pdo()->prepare('UPDATE `users` SET `password` = :password WHERE `login` = :username');
        $stmt->execute([
            ':username' => $_POST['username'],
            ':password' => $newHash,
        ]);
    }
    $_SESSION['login'] = $user['login'];
    echo '1';
    die;
}

?>