<?php
$host = 'mysql'; // Le nom du service Docker MySQL
$dbname = 'db_refuge_animaux';
$username = 'greta';
$password = 'greta_refuge';
$dsn = "mysql:host=$host;port=3306;dbname=$dbname;charset=utf8;allowPublicKeyRetrieval=true";

try {

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {

    die("Erreur de connexion : " . $e->getMessage());
}
