<?php

session_start();

function pdo(): PDO
{
    static $pdo;

    if (!$pdo) {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=gallery;charset=utf8mb4','root');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            print "Connection failed: " . $e->getMessage();
        }
    }

    return $pdo;
}

function check_auth(): bool
{
    return !!($_SESSION['login'] ?? false);
}
                
?>