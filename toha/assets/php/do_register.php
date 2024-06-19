<?php

require_once __DIR__.'/boot.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['username'];
    $password = $_POST['password'];
    $passwordagain = $_POST['password2'];
}

if($password != $passwordagain) {
    echo '0';
    die;
}

$stmt = pdo()->prepare("SELECT * FROM `users` WHERE `login` = :username");
$stmt->execute([':username' => $email]);
if ($stmt->rowCount() > 0) {
    echo '1';
    die; 
}

$stmt = pdo()->prepare("INSERT INTO users (login, password, rank) VALUES (:username, :pass, :rank)");
$stmt->execute([':username' => $email,':pass' => password_hash($password, PASSWORD_DEFAULT),':rank' => False]);
echo '2';

?>