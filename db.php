<?php
// Read connection details from Render's environment variables
$host = getenv('mysql-3b48cc1b-akjaz400-aef8.l.aivencloud.com');
$db   = getenv('defaultdb');
$user = getenv('avnadmin');
$pass = getenv('AVNS_srxVc9emX9DyoZWuy0V');
$port = getenv('19669');
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
