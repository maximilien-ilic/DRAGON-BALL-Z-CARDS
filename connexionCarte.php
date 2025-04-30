<?php
///connexion base de donnée
$host = 'localhost';
$dbname = 'cartes';
$username = 'root';
$password = '';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    ///var_dump($pdo);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

session_start();
